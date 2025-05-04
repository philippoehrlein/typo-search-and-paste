<template>
  <k-dialog
    :cancel-button="false"
    :submit-button="false"
    :visible="true"
    size="medium"
    class="k-typo-search-and-paste-dialog"
    role="dialog"
    aria-labelledby="typo-search-and-paste-dialog-title"
    @cancel="emit('cancel')"
  >
    <h2 id="typo-search-and-paste-dialog-title" class="sr-only">
      {{
        panel.t(
          "philippoehrlein.typo-search-and-paste.buttonTitle",
          "Search Special Characters",
        )
      }}
    </h2>
    <TSPSearch
      @result="handleResults"
      @length="handleLength"
      @close="handleClose"
    />
    <TSPResults
      :results="results"
      :query-length="queryLength"
      @close="handleClose"
    />
  </k-dialog>
</template>

<script setup>
import { usePanel } from "kirbyuse";
import { defineEmits, ref } from "vue";
import TSPResults from "./TSPResults.vue";
import TSPSearch from "./TSPSearch.vue";

const emit = defineEmits(["close"]);

const panel = usePanel();

const results = ref([]);
const queryLength = ref(0);

const handleResults = (newResults) => {
  results.value = newResults;
};

const handleLength = (newLength) => {
  queryLength.value = newLength;
};

const handleClose = () => {
  emit("close");
};
</script>

<style>
TSPSearch {
  margin-bottom: var(--spacing-3);
}

.k-typo-search-and-paste-dialog {
  background-color: var(--color-black);
  color: var(--color-white);
  margin: 20vh auto auto !important;
  max-height: 100%;
  padding: 0 !important;
}

.k-typo-search-and-paste-dialog:deep(.k-dialog-body) {
  max-height: 100%;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}
</style>
