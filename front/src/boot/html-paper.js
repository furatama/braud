// import something here
import VueHtmlToPaper from "vue-html-to-paper";

// "async" is optional
export default async ({ Vue }) => {
  const options = {
    name: "_blank",
    specs: ["fullscreen=yes", "titlebar=yes", "scrollbars=yes"],
    styles: [
      // 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
      // "https://unpkg.com/kidlat-css/css/kidlat.css"
    ]
  };

  Vue.use(VueHtmlToPaper, options);
};
