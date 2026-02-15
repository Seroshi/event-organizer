<?php

namespace App\Enums;

enum UserRole: string
{
   case Admin = 'admin';
   case Organizer = 'organizer';
   case User = 'user';

   // Assigns the role restriction levels
   public function accessLevel(): string
   {
      return match($this) {
         self::Admin       => 'full-access',
         self::Organizer   => 'creator-access',
         default           => 'read-only',
      };
   }
   
   // Define the dutch labels for the selected user
   public function label(): string
   {
      return match($this) {
         self::Admin       => 'Admin',
         self::Organizer   => 'Organisator',
         default           => 'Deelnemer',
      };
   }

   // To retrieved an array of all labels if needed
   public static function allLabels(): array
   {
      return array_map(fn($role) => $role->label(), self::cases());
   }

   public function description(): string
   {
      return match($this) {
         self::Admin       => 'Een admin heeft alle toegang. Elke gebruikerspositie en evenement kan gewijzigd worden.',
         self::Organizer   => 'Een organisator heeft beperkte toegang en kan alleen zijn/haar eigen evenementen beheren.',
         default           => 'Een deelnemer heeft geen beheer toegang en kan alleen zijn/haar profiel beheren of evenementen volgen.',
      };
   }

   /** 
    * Use this for a select form to get the right role labels and values 
    */
   public static function options(): array
   {
      $options = [];
      foreach (self::cases() as $role) {
         $options[$role->value] = $role->label();
      }
      return $options;
   }

   public function labelColor(): string
   {
      return match($this) {
         self::Admin       => 'bg-orange-500',
         self::Organizer   => 'color-sub',
         default           => 'bg-teal-700',
      };
   }
}