function check(address, onSuccess, onError, onComplete) {
  $.ajax({
    type: "POST",
    url: "/check/",
    data: {
      wallet: address.trim(),
    },
    timeout: 30000,
    success: function (data) {
      onSuccess();
    },
    error: function (req) {
      onError();
    },
    complete: function (req) {
      onComplete();
    },
  });
}
