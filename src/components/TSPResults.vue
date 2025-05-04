<template>
  <div v-if="queryLength > 2" class="tsp-results-container">
    <div v-if="results.length > 0" class="tsp-results">
    <k-button 
    v-for="result in results" 
    :key="result.value" 
    class="tsp-results__result"
    tabindex="0"
    role="menuitem"
    :title="result.name"
    @click="copyToClipboard(result.value)"
    >
      <span class="tsp-results__result-value">{{ result.value }}</span>
      <span class="tsp-results__result-name">{{ result.name }}</span>
    </k-button>
  </div>
  <div v-else class="tsp-results__no-results">
    <p>No results found</p>
  </div>
  </div>
</template>

<script setup>
import { usePanel } from "kirbyuse";

defineProps({
  results: {
    type: Array,
    required: true
  },
  queryLength: {
    type: Number,
    required: true
  }
})
const emit = defineEmits(['close']);

const panel = usePanel();




function copyToClipboard(character) {
  navigator.clipboard.writeText(character);

  emit("close");

  panel.notification.info({
    message: panel.t("philippoehrlein.typo-search-and-paste.copiedMessage", {
      character,
    }),
    icon: undefined,
  });
}
</script>

<style scoped>
.tsp-results-container {
  display:block;
  height: 100%;
  width: 100%;
  max-height: 50vh;
  overflow-y: auto;
  border-top: 1px solid var(--dropdown-color-hr);
  padding: var(--spacing-3) 0;
}

.tsp-results {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-1);
  height: 100%;
  padding: 0 2px;
}

.tsp-results__no-results {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--spacing-3);
}

.tsp-results__result {
  width: 100%;
}

.tsp-results__result:hover {
  background-color: var(--dropdown-color-hr);
}

.tsp-results__result:deep(.k-button-text) {
  display: flex;
  flex-direction: row;
  align-items: center;
  width: 100%;
  gap: var(--spacing-3);
}

.tsp-results__result-value {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;

}

.tsp-results__result-name {
  flex-grow: 1;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
  text-align: left;
}

</style>