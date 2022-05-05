<template>
  <div>
  </div>
</template>

<script>
export default {
  props: ['title', 'msgSuccess', 'msgFailed'],
  mounted() {
    var url_string = window.location.href;
    var url = new URL(url_string);
    var message = url.searchParams.get("message");
    message = (message) ? message : "";
    if (url_string.includes("success")) {
      if ($('.invalid-feedback').length == 0) {
        this.$bvToast.toast(`${this.msgSuccess}: ${message}`, {
          title: `${this.title}`,
          variant: "success",
          autoHideDelay: 5000,
          solid: true
        })
      }
      else {
        this.$bvToast.toast(`${this.msgFailed}`, {
          title: `${this.title}`,
          variant: "error",
          autoHideDelay: 5000,
          solid: true
        })
      }
    }
  }
}
</script>

