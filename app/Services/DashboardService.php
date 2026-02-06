<?php

namespace App\Services;

use App\Models\User;
use App\Models\Profile;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DashboardService
{
   /**
    * Handle the core logic for updating an profile.
    */
   public function updateProfile(User $user, Profile $profile, array $data): Profile
   {

      // Update the user record which is part of profile 
      $user->update([
         'name'   => $data['profileName'],
         'email'  => $data['profileEmail'],
      ]);

      // Update the profile record in the database [note: tap() to ensure a model return]
      return tap($profile)->update([
         'company'    => $data['profileCompany'],
      ]);
   }

   /**
    * Handle the logic for uploading an image with Spatie Media.
    */
   public function uploadImage(Profile $profile, $file): media
   {
      // Spatie handles the TemporaryUploadedFile automatically
      return $profile->addMedia($file)->toMediaCollection('profiles');
   }
}