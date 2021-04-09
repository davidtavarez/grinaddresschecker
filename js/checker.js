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
      console.log("success");
      console.log(status);
      onSuccess();
    },
    error: function (data, status) {
      console.log("error");
      onError();
    },
    complete: function (xhr, status) {
      console.log("complete");
      console.log(status);
      onComplete();
    },
  });
}
