function check(address, onSuccess, onError, onComplete) {
  $.ajax({
    timeout: 30000,
    type: "POST",
    url: "/check/",
    dataType: "json",
    data: {
      wallet: address,
    },
    success: function (data) {
      onSuccess();
    },
    error: function (data) {
      onError();
    },
    complete: function (req) {
      onComplete();
    },
  });
}
