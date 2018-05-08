<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\User;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/member")
 */
class MemberController extends Controller
{

	private $apiKey = "uaycarmkmrq3f3rdguk2mv8xx2ccmgd7";
	private $memberRaideurRank = 4;

    /**
     * @Route("/", name="member_index", methods="GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function index(MemberRepository $memberRepository): Response
    {
        return $this->render("member/index.html.twig", ['members' => $memberRepository->findBy([], ["name" => "ASC"])]);
    }

	/**
	 * @Route("/update/check", name="member_update_check", methods="GET")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
    public function updateCheck(MemberRepository $memberRepository): Response
    {

	    $api_members_by_name = [];
	    $api_members_names = [];
	    $members_by_name = [];
	    $members_names = [];
	    $members_names_to_add = [];
	    $members_names_to_remove = [];



	    $ch = curl_init();
	    curl_setopt_array($ch, [
		    CURLOPT_URL => "https://eu.api.battle.net/wow/guild/Hyjal/Le%20Monde%20Des%20Hauts%20Faits?fields=members&locale=fr_FR&apikey=" . $this->apiKey,
		    CURLOPT_RETURNTRANSFER => true
	    ]);
	    $str_response = curl_exec($ch);

	    if($str_response === false){
		    http_response_code(500);
		    exit("Blizzard api call error.");
	    }

	    curl_close($ch);

	    $response = json_decode($str_response);

	    foreach($response->members as $member){
		    if($member->rank != $this->memberRaideurRank){
			    continue;
		    }

		    $api_members_by_name[$member->character->name] = $member;
		    $api_members_names[] = $member->character->name;

	    }



	    $members = $memberRepository->findAll();

	    foreach($members as $member){
		    $members_by_name[$member->getName()] = $member;
		    $members_names[] = $member->getName();
	    }



	    foreach($api_members_names as $api_member_name){
		    if(!in_array($api_member_name, $members_names)){
			    $members_names_to_add[] = $api_member_name;
		    }
	    }
	    foreach($members_names as $member_name){
		    if(!in_array($member_name, $api_members_names)){
			    $members_names_to_remove[] = $member_name;
		    }
	    }



	    return $this->render("member/check.html.twig", [
	    	'members_names_to_remove' => $members_names_to_remove,
		    'members_names_to_add' => $members_names_to_add,
		    'api_response' => $str_response
	    ]);

    }

	/**
	 * @Route("/update", name="member_update", methods="POST")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function update(Request $request, MemberRepository $memberRepository, ObjectManager $objectManager, UserPasswordEncoderInterface $encoder): Response
	{

		$request_params = $request->request->all();
		$api_response = json_decode($request_params["api_response"]);
		$members_names_to_add = json_decode($request_params["members_names_to_add"]);
		$members_names_to_remove = json_decode($request_params["members_names_to_remove"]);

		foreach($api_response->members as $api_member){
			if(!in_array($api_member->character->name, $members_names_to_add)){
				continue;
			}

			$member = new Member();
			$member->setName($api_member->character->name);
			$member->setClass($api_member->character->class);
			$member->setRace($api_member->character->race);
			$member->setGender($api_member->character->gender);
			$member->setLevel($api_member->character->level);
			$member->setThumbnail($api_member->character->thumbnail);

			if(isset($request_params["create_account"]) && $request_params["create_account"] == "on"){

				$user = new User();
				$user->setUsername($api_member->character->name);
				$user->setPassword($encoder->encodePassword($user, "123456"));
				$user->setRoles(["ROLE_MEMBER"]);

				$objectManager->persist($user);

				$member->setUser($user);

			}

			$objectManager->persist($member);

		}

		foreach($members_names_to_remove as $to_remove){
			$member = $memberRepository->findOneBy(["name" => $to_remove]);
			$objectManager->remove($member);
		}

		$objectManager->flush();

		return $this->redirectToRoute("member_index");

	}

	/**
	 * @Route("/new", name="member_new", methods="GET|POST")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function new(Request $request, ObjectManager $objectManager, UserPasswordEncoderInterface $encoder, AuthorizationCheckerInterface $authorizationChecker): Response
	{
		$member = new Member();
		$form = $this->createForm(MemberType::class, $member, [
			'action' => "new",
			'admin' => $authorizationChecker->isGranted("ROLE_ADMIN")
		]);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()){

			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => "https://eu.api.battle.net/wow/character/Hyjal/" . $member->getName() . "?locale=fr_FR&apikey=" . $this->apiKey,
				CURLOPT_RETURNTRANSFER => true
			]);
			$str_response = curl_exec($ch);

			if($str_response === false){
				http_response_code(500);
				exit("Blizzard api call error.");
			}

			curl_close($ch);

			$response = json_decode($str_response);

			$member->setThumbnail($response->thumbnail);

			if($form->get('create_account')->getData() === "on"){

				$user = new User();
				$user->setUsername($member->getName());
				$user->setPassword($encoder->encodePassword($user, "123456"));
				$user->setRoles(["ROLE_MEMBER"]);
				$objectManager->persist($user);

				$member->setUser($user);

			}

			$objectManager->persist($member);

			$objectManager->flush();

			return $this->redirectToRoute("member_edit", ["id" => $member->getId()]);
		}

		return $this->render("member/new.html.twig", [
			'member' => $member,
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/{id}", name="member_show", methods="GET", requirements={"id"="\d+"})
	 */
	public function show(Member $member): Response
	{
		return $this->render('member/show.html.twig', ['member' => $member]);
	}

	/**
	 * @Route("/{id}/edit", name="member_edit", methods="GET|POST", requirements={"id"="\d+"})
	 * @Security("has_role('ROLE_ADMIN') or user.getId() == member.getUser().getId()")
	 */
	public function edit(Request $request, Member $member, ObjectManager $objectManager, AuthorizationCheckerInterface $authorizationChecker): Response
	{
		$form = $this->createForm(MemberType::class, $member, [
			'action' => "edit",
			'admin' => $authorizationChecker->isGranted("ROLE_ADMIN")
		]);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()){
			$objectManager->flush();

			return $this->redirectToRoute('member_edit', ['id' => $member->getId()]);

		}

		return $this->render("member/edit.html.twig", [
			'member' => $member,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="member_delete", methods="DELETE", requirements={"id"="\d+"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function delete(Request $request, Member $member, ObjectManager $objectManager): Response
	{

		if($this->isCsrfTokenValid('delete'.$member->getId(), $request->request->get('_token'))){
			$objectManager->remove($member);
			$objectManager->flush();
		}

		return $this->redirectToRoute("member_index");

	}

	/**
	 * @Route("/day", name="member_day", methods="GET|POST")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function byDay(Request $request)
	{

		$day = $request->get('day');

		$members = [];

		if($day != null){
			$members = $this->getDoctrine()->getRepository(Member::class)->findByDay($day);
		}

		return $this->render("member/day.html.twig", [
			'members' => $members,
			'day' => $day
		]);

	}
}
