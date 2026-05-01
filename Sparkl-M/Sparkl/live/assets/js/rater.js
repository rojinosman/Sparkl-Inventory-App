import utils from "../../src/js/utils";
/* -------------------------------------------------------------------------- */

/*                               Ratings                               */

/* -------------------------------------------------------------------------- */

const ratingInit = () => {
  const raters = document.querySelectorAll("[data-rater]");
  if($(raters).length > 0 && 1==2) {
    raters.forEach((rater) => {
      let options = {
        reverse: utils.getItemFromStore('isRTL'),
        starSize: 32,
        step: 0.5,
        element: rater,
        rateCallback(rating, done) {
          this.setRating(rating);
          done();
        },
        ...utils.getData(rater, "rater"),
      };

      return window.raterJs(options);
    });
  }



};
export default ratingInit;
