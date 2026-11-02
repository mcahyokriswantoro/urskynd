{{-- Image uploader for repeater items. Requires: $list (array name), $field (image key), optional $round --}}
@php $item = $list . '[i]'; @endphp
<div class="space-y-2">
    <div class="relative group {{ !empty($round) ? 'w-20 h-20 rounded-full' : 'w-full h-32 rounded-xl' }} border-2 border-dashed border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center">
        <template x-if="{{ $item }}._preview || {{ $item }}.{{ $field }}">
            <img :src="{{ $item }}._preview || imageUrl({{ $item }}.{{ $field }})" class="w-full h-full object-cover">
        </template>
        <template x-if="!({{ $item }}._preview || {{ $item }}.{{ $field }})">
            <span class="text-[10px] text-slate-400 text-center px-1">Belum ada foto</span>
        </template>
        <label class="absolute inset-0 bg-black/50 text-white text-[11px] font-semibold flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
            Ganti Foto
            <input type="file" accept="image/*" class="hidden"
                   :name="`{{ $list }}[${i}][{{ $field }}_file]`"
                   @change="const f = $event.target.files[0]; if (f) { {{ $item }}._preview = URL.createObjectURL(f); }">
        </label>
    </div>
    <input type="hidden" :name="`{{ $list }}[${i}][{{ $field }}]`" :value="{{ $item }}.{{ $field }} || ''">
    <div class="flex items-center gap-2">
        <label class="text-[11px] font-semibold text-[#9A6A52] hover:underline cursor-pointer"
               @click.prevent="$el.closest('.space-y-2').querySelector('input[type=file]').click()">Upload</label>
        <button type="button" x-show="{{ $item }}._preview || {{ $item }}.{{ $field }}"
                @click="{{ $item }}.{{ $field }} = null; {{ $item }}._preview = null; $el.closest('.space-y-2').querySelector('input[type=file]').value = ''"
                class="text-[11px] font-semibold text-rose-500 hover:underline">Hapus</button>
    </div>
</div>
