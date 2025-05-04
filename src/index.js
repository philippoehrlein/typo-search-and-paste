import TSPButton from "./components/TSPButton.vue";
import TSPanel from "./components/TSPPanel.vue";
import { icons } from "./config/icons";

window.panel.plugin("philippoehrlein/typo-search-and-paste", {
  icons,
  components: {
    "k-typo-search-and-paste-dialog": TSPanel,
  },
  viewButtons: {
    "typo-search-and-paste": TSPButton,
  },
});
