<template>
  <div class="relative" ref="wrapper">
    <input
      v-model="search"
      @focus="open = true"
      @input="open = true"
      type="text"
      placeholder="Type to search cities..."
      :class="inputClass"
      autocomplete="off"
    />
    <Transition
      enter-active-class="transition ease-out duration-150"
      enter-from-class="opacity-0 -translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-1"
    >
      <ul
        v-if="open && filtered.length"
        class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-xl bg-[#1a1a1f] border border-white/[0.08] shadow-2xl backdrop-blur-xl"
      >
        <li
          v-for="city in filtered"
          :key="city"
          @mousedown.prevent="pick(city)"
          class="px-3 py-2 text-sm text-nexus-300 hover:bg-nexus-500/10 hover:text-nexus-100 cursor-pointer transition-colors first:rounded-t-xl last:rounded-b-xl"
        >
          {{ city }}
        </li>
      </ul>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  inputClass: { type: [String, Array, Object], default: 'input-premium' },
});

const emit = defineEmits(['update:modelValue']);

const cities = [
  'Prishtinë', 'Prizren', 'Pejë', 'Mitrovicë', 'Gjilan', 'Ferizaj',
  'Gjakovë', 'Podujevë', 'Vushtrri', 'Suharekë', 'Rahovec', 'Drenas',
  'Lipjan', 'Malishevë', 'Kamenicë', 'Viti', 'Deçan', 'Istog',
  'Klinë', 'Skenderaj', 'Dragash', 'Fushë Kosovë', 'Kaçanik',
  'Shtime', 'Obiliq', 'Hani i Elezit', 'Mamushë', 'Junik',
  'Kllokot', 'Ranillug', 'Partesh', 'Novobërdë', 'Zubin Potok',
  'Zveçan', 'Leposaviq', 'Graçanicë', 'Shtërpcë', 'Mitrovicë e Veriut',
];

const search = ref(props.modelValue);
const open = ref(false);
const wrapper = ref(null);

const filtered = computed(() => {
  if (!search.value) return cities;
  const q = search.value.toLowerCase();
  return cities.filter(c => c.toLowerCase().includes(q));
});

function pick(city) {
  search.value = city;
  emit('update:modelValue', city);
  open.value = false;
}

// Sync when parent changes modelValue (e.g. Smart Order auto-fill)
watch(() => props.modelValue, (val) => {
  if (val !== search.value) search.value = val;
});

// Emit on manual typing too (in case they type an exact match or custom city)
watch(search, (val) => {
  emit('update:modelValue', val);
});

// Close dropdown on outside click
function onClickOutside(e) {
  if (wrapper.value && !wrapper.value.contains(e.target)) {
    open.value = false;
  }
}
onMounted(() => document.addEventListener('mousedown', onClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside));
</script>
