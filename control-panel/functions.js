EcwidApp.init({
  app_id: "paystack", // use your application namespace
  autoloadedflag: true,
  autoheight: true
});

var storeData = EcwidApp.getPayload();

var storeId = storeData.store_id;
var accessToken = storeData.access_token;
var language = storeData.lang;
var viewMode = storeData.view_mode;

if (storeData.public_token !== undefined) {
  var publicToken = storeData.public_token;
}

if (storeData.app_state !== undefined) {
  var appState = storeData.app_state;
}

// Default settings for new accounts

var initialConfig = {
  testMode: "true",
  testSecretKey: " ",
  testPublicKey: " ",
  liveSecretKey: " ",
  livePublicKey: " ",
  exists: "yes"
};

var loadedConfig = initialConfig;

// Executes when we have a new user install the app. It creates and sets the default data using Ecwid JS SDK and Application storage

function createUserData() {
  EcwidApp.setAppStorage(initialConfig, function(lala) {
    console.log("Initial user preferences saved!");
    console.log(lala);
  });

  document.querySelector('div#toggle input[type="checkbox"]').checked =
    initialConfig.testMode;
  document.querySelector("#test_secret").value = initialConfig.testSecretKey;
  document.querySelector("#test_public").value = initialConfig.testPublicKey;
  document.querySelector("#live_secret").value = initialConfig.liveSecretKey;
  document.querySelector("#live_public").value = initialConfig.livePublicKey;
  document
    .querySelector("div#testMode .field__input")
    .parentNode.classList.add("field--filled");
  document
    .querySelector("div#liveMode .field__input")
    .parentNode.classList.add("field--filled");

  // Setting flag to determine that we already created and saved defaults for this user
  loadedConfig = initialConfig;
}

// Executes if we have a user who logs in to the app not the first time. We load their preferences from Application storage with Ecwid JS SDK and display them in the app iterface

function getUserData() {
  EcwidApp.getAppStorage("testMode", function(testMode) {
    loadedConfig.testMode = testMode;
  });

  EcwidApp.getAppStorage("testSecretKey", function(testSecretKey) {
    loadedConfig.testSecretKey = testSecretKey;
  });

  EcwidApp.getAppStorage("testPublicKey", function(testPublicKey) {
    loadedConfig.testPublicKey = testPublicKey;
  });

  EcwidApp.getAppStorage("testSecretKey", function(liveSecretKey) {
    loadedConfig.liveSecretKey = liveSecretKey;
  });

  EcwidApp.getAppStorage("testSecretKey", function(livePublicKey) {
    loadedConfig.livePublicKey = livePublicKey;
  });

  setTimeout(function() {
    document.querySelector('div#toggle input[type="checkbox"]').checked =
      loadedConfig.testMode == "true";
    document.querySelector('div#testMode input[type="text"]').value =
      loadedConfig.testMode;
    document.querySelector('div#liveMode input[type="text"]').value =
      loadedConfig.liveMode;
    document
      .querySelector("div#testMode .field__input")
      .parentNode.classList.add("field--filled");
    document
      .querySelector("div#liveMode .field__input")
      .parentNode.classList.add("field--filled");
  }, 1500);
}

// Executes when we need to save data. Gets all elements' values and saves them to Application storage via Ecwid JS SDK

function saveUserData() {
  var d = document.getElementById("save");
  d.className += " btn-loading";

  var saveData = loadedConfig;

  saveData.testMode = String(
    document.querySelector('div#toggle input[type="checkbox"]').checked
  );
  saveData.testSecretKey = document.querySelector("#test_secret").value;
  saveData.testPublicKey = document.querySelector("#test_public").value;
  saveData.liveSecretKey = document.querySelector("#live_secret").value;
  saveData.livePublicKey = document.querySelector("#live_public").value;

  console.log(saveData);

  EcwidApp.setAppStorage(saveData, function(savedData) {
    console.log("User preferences saved!");
    console.log(savedData);
    d.className = "btn btn-primary btn-large";
  });
}

function toggleMode() {
  var test_mode = document.getElementById("testMode");
  var live_mode = document.getElementById("liveMode");

  if (document.querySelector('div#toggle input[type="checkbox"]').checked) {
    live_mode.style.display = "block";
    test_mode.style.display = "none";
  } else {
    live_mode.style.display = "none";
    test_mode.style.display = "block";
  }
}
