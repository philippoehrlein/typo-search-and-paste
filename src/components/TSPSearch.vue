<template>
  <div class="tsp-search">
    <k-search-input
      ref="searchInput"
      :placeholder="
        panel.t(
          'philippoehrlein.typo-search-and-paste.searchPlaceholder',
          'Search for special characters',
        )
      "
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
import { onMounted, onUnmounted, ref, usePanel } from "kirbyuse";
import { defineEmits } from "vue";

const emit = defineEmits(["result", "length", "close", "focusresults"]);

const panel = usePanel();
const value = ref("");
const results = ref([]);
const searchInput = ref(null);
let searchTimeout = null;

const search = async (query) => {
  if (query.length < 3) {
    results.value = [];
    return;
  }
  const q = query.trim().replace(/\s+/g, " AND ");
  const safeQuery = encodeURIComponent(q);
  const response = await window.panel.api.get(`tsp-search/${safeQuery}`);
  try {
    results.value = response.results;
    emit("result", results.value);
    emit("length", query.length);
  } catch (error) {
    console.error(error);
  }
};

const onInput = (newValue) => {
  value.value = newValue;

  if (newValue.length < 3) {
    results.value = [];
    emit("length", newValue.length);
    emit("result", results.value);
    return;
  }

  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  searchTimeout = setTimeout(() => {
    search(newValue);
  }, 300);
};

const handleKeydown = (event) => {
  const { key } = event;
  if (key === 'ArrowDown' && results.value.length > 0) {
    event.preventDefault();
    emit('focusresults');
  } else if (key === 'Escape') {
    event.preventDefault();
    emit('close');
  }
};

onMounted(() => {
  // Add keydown listener to the actual input element
  if (searchInput.value && searchInput.value.$el) {
    // $el is already the input element, no need for querySelector
    searchInput.value.$el.addEventListener('keydown', handleKeydown);
  }
});

onUnmounted(() => {
  // Clean up event listener
  if (searchInput.value && searchInput.value.$el) {
    searchInput.value.$el.removeEventListener('keydown', handleKeydown);
  }
});

// Expose methods for parent component
defineExpose({
  focus: () => {
    if (searchInput.value) {
      searchInput.value.focus();
    }
  }
});
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
