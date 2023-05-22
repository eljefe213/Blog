// any SCSS you import will output into a single css file (app.css in this case)
import "../css/app.scss";
import { Dropdown } from "bootstrap";

document.addEventListener("DOMContentLoaded", () => {
  new App();
});

class App {
  constructor() {
    this.enableDropDowns();
    this.handleCommentForm();
  }

  enableDropDowns() {
    const dropdownElementList = [].slice.call(
      document.querySelectorAll(".dropdown-toggle")
    );
    dropdownElementList.map(
      (dropdownToggleEl) => new Dropdown(dropdownToggleEl)
    );
  }

  handleCommentForm() {
    const commentForm = document.querySelector("form.comment-form");

    if (null == commentForm) {
      return;
    }

    commentForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const response = await fetch("/ajax/comments", {
        method: "POST",
        body: new FormData(e.target),
      });

      if (!response.ok) {
        return;
      }
      const json = await response.json();
      console.log(json);
    });
  }
}
