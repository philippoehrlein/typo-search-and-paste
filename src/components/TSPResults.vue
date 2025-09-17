<template>
  <div v-if="queryLength > 2" class="tsp-results-container">
    <div v-if="results.length > 0" class="tsp-results">
      <k-button
        v-for="(result, index) in results"
        :key="result.value"
        :ref="(el) => setButtonRef(el, index)"
        class="tsp-results__result"
        :class="{ 'tsp-results__result--active': activeIndex === index }"
        tabindex="0"
        role="menuitem"
        :title="result.name"
        @click="copyToClipboard(result.value)"
        @keydown.native="handleKeydown"
      >
        <span class="tsp-results__result-value">{{ result.value }}</span>
        <span class="tsp-results__result-name">{{ result.name }}</span>
      </k-button>
    </div>
    <div v-else class="tsp-results__no-results">
      <p>{{ panel.t("philippoehrlein.typo-search-and-paste.noResults") }}</p>
    </div>
  </div>
</template>

<script setup>
import { nextTick, ref, usePanel, watch } from "kirbyuse";

const props = defineProps({
  results: {
    type: Array,
    required: true,
  },
  queryLength: {
    type: Number,
    required: true,
  },
});
const emit = defineEmits(["close", "focusinput"]);

const panel = usePanel();
const activeIndex = ref(-1);
const buttonRefs = ref([]);

const setButtonRef = (el, index) => {
  if (el) {
    buttonRefs.value[index] = el;
  }
};

// Reset active index when results change
watch(() => props.results, () => {
  activeIndex.value = -1;
});

const focusButton = (index) => {
  nextTick(() => {
    if (buttonRefs.value[index]) {
      buttonRefs.value[index].$el?.focus();
    }
  });
};

const handleKeydown = (event) => {
  const { key } = event;
  
  if (key === 'ArrowDown') {
    event.preventDefault();
    if (activeIndex.value < props.results.length - 1) {
      activeIndex.value++;
      focusButton(activeIndex.value);
    }
  } else if (key === 'ArrowUp') {
    event.preventDefault();
    if (activeIndex.value > 0) {
      activeIndex.value--;
      focusButton(activeIndex.value);
    } else {
      // Focus back to input
      emit('focusinput');
    }
  } else if (key === 'Escape') {
    event.preventDefault();
    emit('close');
  }
};

// Expose methods for parent component
defineExpose({
  focusFirst: () => {
    if (props.results.length > 0) {
      activeIndex.value = 0;
      focusButton(0);
    }
  },
  focusLast: () => {
    if (props.results.length > 0) {
      activeIndex.value = props.results.length - 1;
      focusButton(props.results.length - 1);
    }
  }
});

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
  display: block;
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

.tsp-results__result:hover,
.tsp-results__result--active {
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
