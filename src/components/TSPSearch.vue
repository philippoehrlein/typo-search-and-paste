<template>
  <div class="tsp-search">
    <k-search-input 
      :placeholder="panel.t('philippoehrlein.typo-search-and-paste.searchPlaceholder', 'Search for special characters')"
      :value="value"
      autofocus
      @input="onInput"
    />
    <k-button
				:icon="isLoading ? 'loader' : 'cancel'"
				:title="$t('close')"
				class="k-search-bar-close"
				@click="$emit('close')"
			/>
  </div>
</template>

<script setup>
import { usePanel } from "kirbyuse";
import { defineEmits, ref } from "vue";
const emit = defineEmits(['result', 'length', 'close']);

const panel = usePanel();
const value = ref("");
const results = ref([]);
let searchTimeout = null;

const search = async (query) => {
  if (query.length < 3) {
    results.value = [];
    return;
  }
  const q = query.trim().replace(/\s+/g, ' AND ');
  const safeQuery = encodeURIComponent(q);
  const response = await window.panel.api.get(`tsp-search/${safeQuery}`);
  try {
    results.value = response.results;
    emit('result', results.value);
    emit('length', query.length);
  } catch (error) {
    console.error(error);
  }
};

const onInput = (newValue) => {
  value.value = newValue;
  
  if( newValue.length < 3) {
    results.value = [];
    emit('length', newValue.length);
    emit('result', results.value);
    return;
  }

  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  
  searchTimeout = setTimeout(() => {
    search(newValue);
  }, 300);
};


</script>

<style>
.tsp-search {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: var(--spacing-3);
  margin-bottom: var(--spacing-1);
}
</style>