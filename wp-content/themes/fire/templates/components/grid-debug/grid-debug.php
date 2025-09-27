<div x-cloak x-data='{visible: Alpine.$persist(false).as("grid_debug").using(sessionStorage), gridVisible:false }' class="grid-stack fixed inset-0 pointer-events-none z-[10005]">
  <div class="flex pointer-events-auto gap-1 z-[10001] fixed bottom-2 right-2" >
    <button :class="!visible && 'opacity-0 pointer-events-none translate-y-full'" type="button" @click="gridVisible = !gridVisible"
      class="duration-300 ease-in-out transition-all px-4 py-2 bg-white/80 shrink-0 border-black/50 border rounded-full text-[1rem] hover:bg-white">
      Example
    </button>
    <button type="button" @click="visible = !visible"
      class="px-4 py-2 bg-white/80 shrink-0 text-black flex items-center justify-center border-black/50 border rounded-full text-[1rem] hover:bg-white" :class="visible && '!bg-[#01a27f]'" aria-label="Toggle Grid">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-0.5" viewBox="0 0 448 512" fill="currentColor">
        <path  d="M224 32H32V224H224V32zm0 256H32V480H224V288zM288 32V224H480V32H288zM480 288H288V480H480V288z"/></svg>
    </button>
  </div>
  <div class="h-screen fire-container" x-cloak>
    <div x-show="visible" class='col-[col-1] row-span-full bg-[#01a27f]/30 flex items-end justify-center text-black/50 py-4'>1</div>
    <div x-show="visible" class='col-[col-2] row-span-full bg-[#01a27f]/30 flex items-end justify-center text-black/50 py-4'>2</div>
    <div x-show="visible" class='col-[col-3] row-span-full bg-[#01a27f]/30 flex items-end justify-center text-black/50 py-4'>3</div>
    <div x-show="visible" class='col-[col-4] row-span-full bg-[#01a27f]/30 flex items-end justify-center text-black/50 py-4'>4</div>
    <div x-show="visible" class='hidden col-[col-5] row-span-full bg-[#01a27f]/30 md:flex items-end justify-center text-black/50 py-4'>5</div>
    <div x-show="visible" class='hidden col-[col-6] row-span-full bg-[#01a27f]/30 md:flex items-end justify-center text-black/50 py-4'>6</div>
    <div x-show="visible" class='hidden col-[col-7] row-span-full bg-[#01a27f]/30 md:flex items-end justify-center text-black/50 py-4'>7</div>
    <div x-show="visible" class='hidden col-[col-8] row-span-full bg-[#01a27f]/30 md:flex items-end justify-center text-black/50 py-4'>8</div>
    <div x-show="visible" class='hidden col-[col-9] row-span-full bg-[#01a27f]/30 md:flex items-end justify-center text-black/50 py-4'>9</div>
    <div x-show="visible" class='hidden col-[col-10] row-span-full bg-[#01a27f]/30 md:flex items-end justify-center text-black/50 py-4'>10</div>
    <div x-show="visible" class='hidden col-[col-11] row-span-full bg-[#01a27f]/30 md:flex items-end justify-center text-black/50 py-4'>11</div>
    <div x-show="visible" class='hidden col-[col-12] row-span-full bg-[#01a27f]/30 md:flex items-end justify-center text-black/50 py-4'>12</div>
  </div>
  <div class="max-h-screen fire-container gap-y-2 auto-rows-min md:py-20 opacity-90" x-show="gridVisible && visible" x-cloak>
    <div class="p-5 font-bold text-left bg-gray-400">No defined class</div>
    <div class="p-5 font-bold text-left bg-gray-400 full-width">
      <div class="p-5 font-bold text-left bg-gray-400">.full-width</div>
    </div>
    <div class="p-5 font-bold text-left bg-gray-400 left">.left</div>
    <div class="p-5 font-bold text-left bg-gray-400 right">.right</div>
    <div class="p-5 font-bold text-left bg-gray-400 border-r border-dashed left md:suck-right">.left .suck-right</div>
    <div class="p-5 font-bold text-left bg-gray-400 border-l border-dashed right md:suck-left">.right .suck-right</div>
    <div class="grid p-5 font-bold text-left bg-gray-400 left-breakout grid-cols-subgrid">
      <div class="mb-5 col-span-full">.left-breakout section with subgrid</div>
      <div class="col-[col-1/col-4] md:col-[col-1/col-5] text-left p-5 bg-gray-600 text-white font-bold grid grid-cols-subgrid">
        <div class="mb-5 col-span-full">.col-[col-1/col-5] subgrid</div>
        <div class="col-[col-2/col-4] text-left p-5 bg-gray-700 text-white font-bold ">
          .col-[col-2/col-4] subgrid
        </div>
      </div>
    </div>
    <div class="p-5 font-bold text-left bg-gray-400 right-breakout">.right-breakout section</div>
    <div class="p-5 font-bold text-left bg-gray-400 border-r border-dashed left-breakout md:suck-right">.left-breakout with .suck-right (touch in middle)</div>
    <div class="p-5 font-bold text-left bg-gray-400 border-l border-dashed right-breakout md:suck-left">.right-breakout with .suck-left (touch in middle)</div>
    <div class="col-[full-width] md:col-[full-width/col-3] text-left p-5 bg-gray-400 font-bold">.col-[full-width/col-3] custom inline</div>
    <div class="col-[full-width] md:col-[col-4/full-width] text-left p-5 bg-gray-400 font-bold">.col-[col-4/full-width] custom inline</div>
  </div>
</div>