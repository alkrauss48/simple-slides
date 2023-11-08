// @ts-ignore
import textfit from '../lib/textFit.js';

/* c8 ignore next 13 */
export const runTextFit = (element: HTMLDivElement) : void => {
  textfit(element, {
    maxFontSize: 1000,
  });
};

export const openAllLinksInNewTab = () : void => {
  document
    .querySelectorAll(".slide-content a")
    .forEach((element) => {
      element.setAttribute("target", "_blank");
    });
};
