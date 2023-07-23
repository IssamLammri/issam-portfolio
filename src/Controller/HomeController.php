<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form,
            'current_date' => date('j F, Y'),
        ]);
    }

    #[Route('/contact-me', name: 'contact_me')]
    public function contactMe(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setContactedAt(new \DateTimeImmutable());
            $entityManager->persist($contact);
            $entityManager->flush();

            // Return a JSON response indicating the success
            return $this->json(['success' => true]);
        }

        // If the form is not submitted or not valid, return a JSON response with errors
        $formErrors = $this->getFormErrors($form);
        return $this->json(['success' => false, 'errors' => $formErrors]);
    }

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        // Loop through each form field to collect errors
        foreach ($form as $childForm) {
            foreach ($childForm->getErrors(true, true) as $error) {
                $errors[$childForm->getName()][] = $error->getMessage();
            }
        }

        return $errors;
    }
}
