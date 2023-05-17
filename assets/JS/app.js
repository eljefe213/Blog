// any SCSS you import will output into a single css file (app.css in this case)
import "../css/app.scss";
import { Dropdown } from "bootstrap";

document.addEventListener("DOMContentLoaded", () => {
  enableDropDowns();
});

const enableDropDowns = () => {
  const dropdownElementList = [].slice.call(
    document.querySelectorAll(".dropdown-toggle")
  );
  dropdownElementList.map((dropdownToggleEl) => new Dropdown(dropdownToggleEl));
};
