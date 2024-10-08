<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        // Vérification que le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();
            
            // Créer un e-mail simple
            $email = (new Email())
                ->from('noreply@tonentreprise.com')
                ->to('paul@driveasy.com')
                ->replyTo($data['email'])
                ->subject(htmlspecialchars($data['sujet']))
                ->text('Message reçu : ' . $data['content'])
                ->html('
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); overflow: hidden; }
                        .header { background-color: #007BFF; color: white; padding: 20px; text-align: center; font-size: 24px; }
                        .content { padding: 20px; font-size: 16px; color: #333333; line-height: 1.6; }
                        .content p { margin: 0 0 10px; }
                        .footer { background-color: #f4f4f4; padding: 10px; text-align: center; font-size: 12px; color: #777777; }
                    </style>
                </head>
                <body>
                    <div class="email-container">
                        <div class="header">Nouveau Message Reçu</div>
                        <div class="content">
                            <p>Bonjour,</p>
                            <p>Un nouveau message a été soumis via le formulaire de contact :</p>
                            <p><strong>Nom :</strong> ' . htmlspecialchars($data['nom']) . '</p>
                            <p><strong>Prénom :</strong> ' . htmlspecialchars($data['prenom']) . '</p>
                            <p><strong>Numéro :</strong> ' . htmlspecialchars($data['numero']) . '</p>
                            <p><strong>Email :</strong> ' . htmlspecialchars($data['email']) . '</p>
                            <p><strong>Sujet :</strong> ' . htmlspecialchars($data['sujet']) . '</p>
                            <p><strong>Message :</strong></p>
                            <p>' . nl2br(htmlspecialchars($data['content'])) . '</p>
                        </div>
                        <div class="footer">
                            <p>Ce message a été envoyé par ' . htmlspecialchars($data['prenom']) . ' ' . htmlspecialchars($data['nom']) . '.</p>
                            <p>Merci de votre attention.</p>
                        </div>
                    </div>
                </body>
                </html>
            ');
            // Ajouter un en-tête Reply-To
            $email->replyTo($data['email']);
            
            try {
                $mailer->send($email);
                $this->addFlash('success', 'E-mail envoyé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'e-mail');
            }
        }
        

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form->createView(),
            
        ]);
    }
}


