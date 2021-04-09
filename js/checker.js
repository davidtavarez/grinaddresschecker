function check(address, onSuccess, onError, onComplete) {
  $.ajax({
    timeout: 30000,
    type: "POST",
    url: "/check/",
    dataType: "json",
    data: {
      wallet: address,
    },
    success: function (data, status) {
      onSuccess();
    },
    error: function (data, status) {
      onError();
    },
    complete: function (xhr, status) {
      onComplete();
    },
  });
}
