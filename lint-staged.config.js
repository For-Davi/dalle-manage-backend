export default {
  "*.php": (files) => {
    return `./vendor/bin/pint ${files.join(" ")}`;
  },
};
