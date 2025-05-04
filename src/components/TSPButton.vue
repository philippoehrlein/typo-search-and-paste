<template>
  <div>
    <k-button
      :dropdown="false"
      :title="panel.t('philippoehrlein.typo-search-and-paste.buttonTitle')"
      variant="filled"
      size="sm"
      icon="typo-search-and-paste"
      @click="openTSPPanel"
    >
    </k-button>
  </div>
</template>

<script setup>
import { usePanel } from "kirbyuse";
const panel = usePanel();

function openTSPPanel() {
  window.panel.dialog.open({
    component: "k-typo-search-and-paste-dialog",
  });
}
</script>

