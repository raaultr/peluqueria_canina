<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\ActiveUserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class ActiveUserCheckerTest extends TestCase
{
    public function testActiveUserPassesPreAuthentication(): void
    {
        $user = new User();

        (new ActiveUserChecker())->checkPreAuth($user);

        self::assertTrue($user->isActivo());
    }

    public function testSuspendedUserIsRejectedBeforeAuthentication(): void
    {
        $user = (new User())->setActivo(false);

        $this->expectException(CustomUserMessageAccountStatusException::class);

        (new ActiveUserChecker())->checkPreAuth($user);
    }
}