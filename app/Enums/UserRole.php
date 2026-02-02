<?php

namespace App\Enums;

enum UserRole: string
{
   case Master = 'master';
   case Admin = 'admin';
   case Organizer = 'organizer';
   case User = 'user';

   // Assigns the role restriction levels
   public function accessLevel(): string
   {
      return match($this) {
         self::Master,     => 'full-access',
         self::Admin       => 'admin-access',
         self::Organizer   => 'creater-access',
         default           => 'read-only',
      };
   }
   
   public function label(): string
   {
      return match($this) {
         self::Master      => 'Head Aministrator',
         self::Admin       => 'Administrator',
         self::Organizer   => 'Event Creator',
         default           => 'Standard User',
      };
   }
}