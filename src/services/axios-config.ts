import axios from "axios";

axios.defaults.headers.common = {
  "Content-Type": "application/json;charset=utf-8",
};
axios.defaults.baseURL =
  "http://php-vue-project:8080/server/index.php?request_url=";
