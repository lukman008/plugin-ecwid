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
  liveMode: false,
  testSecretKey: " ",
  testPublicKey: " ",
  liveSecretKey: " ",
  livePublicKey: " "
};

var loadedConfig = initialConfig;

// Executes when we have a new user install the app. It creates and sets the default data using Ecwid JS SDK and Application storage
function createUserData() {
  console.log("creating user data");
  EcwidApp.setAppStorage(initialConfig, function(allKeys) {
    console.log("Initial user preferences saved!");
    console.log(allKeys);
  });

  document.querySelector('#toggle input[type="checkbox"]').checked =
    initialConfig.liveMode;
  document.querySelector("#test_secret").value = initialConfig.testSecretKey;
  document.querySelector("#test_public").value = initialConfig.testPublicKey;
  document.querySelector("#live_secret").value = initialConfig.liveSecretKey;
  document.querySelector("#live_public").value = initialConfig.livePublicKey;

  // Setting flag to determine that we already created and saved defaults for this user
  loadedConfig = initialConfig;
}

// Executes if we have a user who logs in to the app not the first time. We load their preferences from Application storage with Ecwid JS SDK and display them in the app interface
function getUserData() {
  console.log("Getting user data");

  EcwidApp.getAppStorage("liveMode", function(liveMode) {
    console.log("Live mode is " + liveMode);
    if (liveMode === "true"){
      document.querySelector("#toggle input[type=checkbox]").checked = true;
      toggleMode();
    }
    else {
      document.querySelector("#toggle input[type=checkbox]").checked = false;
    }
  });

  EcwidApp.getAppStorage("testSecretKey", function(testSecretKey) {
    loadedConfig.testSecretKey = testSecretKey;
    document.getElementById("test_secret").value = loadedConfig.testSecretKey;
  });

  EcwidApp.getAppStorage("testPublicKey", function(testPublicKey) {
    loadedConfig.testPublicKey = testPublicKey;
    document.getElementById("test_public").value = loadedConfig.testPublicKey;
  });

  EcwidApp.getAppStorage("liveTestKey", function(liveTestKey) {
    loadedConfig.liveTestKey = liveTestKey;
    document.getElementById("live_test").value = loadedConfig.liveTestKey;
  });

  EcwidApp.getAppStorage("livePublicKey", function(livePublicKey) {
    loadedConfig.livePublicKey = livePublicKey;
    document.getElementById("live_public").value = loadedConfig.livePublicKey;
  });

  setTimeout(function() {
    //   document.querySelector('div#toggle input[type="checkbox"]').checked =
    //     loadedConfig.liveMode == true;
    //   // if(!loadedConfig.liveMode){
    //   //   toggleMode();
    //   // }
    //   // toggleMode();
    //   document.querySelector("#live_secret").value = loadedConfig.liveSecretKey;
    //   document.querySelector("#live_public").value = loadedConfig.testPublicKey;
    //   document.querySelector("#test_secret").value = loadedConfig.testSecretKey;
    //   document.querySelector("#test_public").value = loadedConfig.testPublicKey;
  }, 10000);
  return loadedConfig;
}

// Executes when we need to save data. Gets all elements' values and saves them to Application storage via Ecwid JS SDK

function saveUserData() {
  console.log("Saving user data");
  var d = document.getElementById("save");
  d.className += " btn-loading";

  var saveData = loadedConfig;

  saveData.liveMode = String(
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

  EcwidApp.closeAppPopup();
}

function toggleMode() {
  var test_mode = document.getElementById("testMode");
  var live_mode = document.getElementById("liveMode");

  if (document.querySelector('#toggle input[type="checkbox"]').checked) {
    live_mode.style.display = "block";
    test_mode.style.display = "none";
  } else {
    live_mode.style.display = "none";
    test_mode.style.display = "block";
  }
}
