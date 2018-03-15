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
    if (liveMode === "true") {
      document.querySelector("#toggle input[type=checkbox]").checked = true;
      toggleMode();
    } else {
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

  EcwidApp.getAppStorage("liveSecretKey", function(liveSecretKey) {
    loadedConfig.liveSecretKey = liveSecretKey;
    document.getElementById("live_secret").value = loadedConfig.liveSecretKey;
  });

  EcwidApp.getAppStorage("livePublicKey", function(livePublicKey) {
    loadedConfig.livePublicKey = livePublicKey;
    document.getElementById("live_public").value = loadedConfig.livePublicKey;
  });
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
  saveData.testSecretKey = String(
    document.querySelector("#test_secret").value
  ).trim();
  saveData.testPublicKey = String(
    document.querySelector("#test_public").value
  ).trim();
  saveData.liveSecretKey = String(
    document.querySelector("#live_secret").value
  ).trim();
  saveData.livePublicKey = String(
    document.querySelector("#live_public").value
  ).trim();

  var cb = function(valid) {
    if (valid) {
      EcwidApp.setAppStorage(saveData, function(savedData) {
        console.log("User preferences saved!");
        console.log(savedData);
      });
      EcwidApp.closeAppPopup();
    } else if (valid === null) {
      document.getElementById("error-message").innerHTML =
        "An error occured while validating your API keys, please try again";
    } else {
      document.getElementById("error-message").innerHTML =
        "The API key pair does not match. Please check your <a href='https://dashboard.paystack.com/#/settings/developer target='_blank'>dashboard</a> and try again";
    }
    d.className = "btn btn-primary btn-large";
  };

  var validated = false;
  if (String(saveData.liveMode) === "true") {
    validated = validate(saveData.liveSecretKey, saveData.livePublicKey, cb);
  } else {
    validated = validate(saveData.testSecretKey, saveData.testPublicKey, cb);
  }
}

function validate(secret_key, public_key, cb) {
  var data = null;

  var xhr = new XMLHttpRequest();
  xhr.withCredentials = true;
  //xhr.

  xhr.addEventListener("readystatechange", function() {
    if (this.readyState === 4) {
      if (this.status === 200) {
        try {
          var result = JSON.parse(this.responseText);
          cb(result.data.public_key === public_key);
        } catch (error) {
          cb(null);
        }
      } else {
        cb(false);
      }
    }
  });

  xhr.open("GET", "https://api.paystack.co/ident", false);
  xhr.setRequestHeader("Authorization", "Bearer " + secret_key);

  //console.log(this.responseText);

  return xhr.send(data);
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
