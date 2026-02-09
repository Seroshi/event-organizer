<?php

use App\Models\Statistic;

if (! function_exists('visitor')) {
   /**
   * Get the currently active visitor for the session.
   */
   function newVisitor()
   {

      // Get the ID from the session
      $visitorId = session('active_visitor_id');

      if(!$visitorId) return null;
   
      static $cachedVisitor;

      static $alreadyLooked = false;

      // Making sure session is only active if DB record exist
      if (!$alreadyLooked) {
         $cachedVisitor = Visitor::find($visitorId);
         $alreadyLooked = true;
      }

      return $cachedVisitor;

    }
}