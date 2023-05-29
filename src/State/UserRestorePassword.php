<?php
# api/src/State/UserRestorePassword.php

namespace App\State;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\Email;
use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\String\ByteString;

final class UserRestorePassword implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor, 
        private readonly UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private Environment $twig,
        private MailerInterface $mailer
    )
    {
        $this->entityManager = $entityManager;
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $operationUri = $operation->getUriTemplate();
        $repository = $this->entityManager->getRepository(User::class);
        if("/auth/forgot" === $operationUri) {
            $user = $repository->findOneBy(['email' => $data->getEmail()]);
            if(!$user) {
                $this->processor->process($data, $operation, $uriVariables, $context);
            }
            $token = ByteString::fromRandom(16, $data->getEmail())->toString();
            $message = (new Email())
            ->from('no-reply@example.com')
            ->to($data->getEmail())
            ->subject('Forgot password')
            ->html($this->twig->render(
                'reset_password/email.html.twig',
                [
                    'resetToken' => $token
                ]
            ));
            if (0 === $this->mailer->send($message)) {
                throw new NotFoundHttpException('Unable to send email');
            }
            $data = $user;
            $data->setRestoreToken($token);
        }
        if("/auth/restore" === $operationUri) {
            $user = $repository->findOneBy(['restoreToken' => $data->getRestoreToken()]);
            if(!$user) {
                return $this->processor->process($data, $operation, $uriVariables, $context);
            }
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $data->getPassword()
            );
            $data = $user;
            $data->setPassword($hashedPassword);
            $data->setRestoreToken("");
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}