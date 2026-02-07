@props(['name'])

<section 
   x-data="{ showModal: false }"
   x-on:open-modal.window="
      showModal = ($event.detail.name === '{{ $name }}')"
   x-on:close-modal.window="if ($event.detail.name === '{{ $name }}') showModal = false"
   x-show="showModal" x-cloak
   style="display: none;"
>

   <div class="fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-[20px] z-60 px-2" 
      background:rgba(0,0,0,1);"
   >
      <div class="bg-zinc-700 py-6 pl-6 mr-3 my-4 rounded-xl border border-zinc-800 w-[90%] max-h-[90vh] flex flex-col md:max-w-xl relative" 
         @click.away="showModal = true"
      >
         
         <!-- Modal - Close button -->
         <div class="absolute -top-3.75 -right-3.75 text-xs text-white w-8 h-8 bg-gray-500 hover:bg-gray-700 rounded-full flex justify-center items-center 
            cursor-pointer shadow-md duration-200"
            @click="showModal = false"
         >
            <flux:icon.x-mark variant="solid" class="size-5" />
         </div>

         <div class="w-full h-full overflow-y-auto hide-scrollbar-until-hover pl-1 pr-6">
            {{ $slot }}
         </div>

      </div>
   </div>

</section>