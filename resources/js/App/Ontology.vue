<template>
  <div class="flex items-start space-x-4">
    <div class="min-w-0 flex-1">
      <form action="#" class="relative">
        <div
          class="border border-gray-300 rounded-lg shadow-sm overflow-hidden focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500"
        >
          <label for="comment" class="sr-only">Add your comment</label>
          <textarea
            id="autocomplete"
            type="text"
            class="vc_textarea block w-full py-3 border-0 resize-none focus:ring-0 sm:text-sm"
          ></textarea>
          <textarea
            @select="onBagClick()"
            type="text"
            name="comment"
            id="mainInput"
            class="block w-full py-3 border-0 resize-none focus:ring-0 sm:text-sm"
            placeholder=""
          />
          <div class="py-2" aria-hidden="true">
            <div class="py-px">
              <div class="h-9" />
            </div>
          </div>
        </div>
        <div class="absolute bottom-0 inset-x-0 pl-3 pr-2 py-2 flex justify-between">
          <div class="flex items-center space-x-5">
            <div class="flex items-center">
              <button
                type="button"
                class="-m-2.5 w-10 h-10 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-500"
              >
                <PaperClipIcon class="h-5 w-5" aria-hidden="true" />
                <span class="sr-only">Attach a file</span>
              </button>
            </div>
            <div class="flex items-center">
                <small>{{highlightedText}}</small>
            </div>
          </div>
          <div class="flex-shrink-0">
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Save
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
#autocomplete {
  opacity: 0.6;
  background: transparent;
  position: absolute;
  box-sizing: border-box;
  cursor: text;
  pointer-events: none;
  color: black;
  border: none;
  left: 2px;
  top: 2px;
}
.vc_textarea {
  padding: 10px;
  min-height: 100px;
  resize: none;
}
#mainInput {
  background: transparent;
  color: black;
  opacity: 1;
}
#autocomplete {
  opacity: 0.5;
  background: transparent;
  padding: 11px 11px 11px 11px;
}
</style>

<script>
import { onMounted, computed, getCurrentInstance } from "vue";
import {
  EmojiHappyIcon,
  EmojiSadIcon,
  FireIcon,
  HeartIcon,
  PaperClipIcon,
  ThumbUpIcon,
  XIcon,
} from "@heroicons/vue/solid";

import {
  Listbox,
  ListboxButton,
  ListboxLabel,
  ListboxOption,
  ListboxOptions,
} from "@headlessui/vue";

export default {
  data() {
    return {
      moods: [],
      highlightedText: "",
    };
  },
  components: {
    EmojiHappyIcon,
    EmojiSadIcon,
    FireIcon,
    HeartIcon,
    PaperClipIcon,
    ThumbUpIcon,
    XIcon,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
  },
  setup(props) {
    onMounted(() => {
      document.addEventListener("DOMContentLoaded", (event) => {
        function queryAPI(term) {
          var get = makeRequestCreator();
          return get("http://127.0.0.1:8000/compose/suggestions/" + term);
          //   return get(
          //     "https://www.ebi.ac.uk:443/metabolights/ws/ebi-internal/ontology?term=" +
          //       term +
          //       "&branch=design%20descriptor&mapping=fuzzy"
          //   );
        }

        function makeRequestCreator() {
          var source;
          return function (url) {
            if (source) {
              source.cancel();
            }
            const CancelToken = axios.CancelToken;
            source = CancelToken.source();

            return axios
              .get(url, { cancelToken: source.token })
              .then((response) => {
                return response;
              })
              .catch(function (thrown) {
                if (axios.isCancel(thrown)) {
                  console.log("First request canceled", thrown.message);
                } else {
                  // handle error
                }
              });
          };
        }

        function scrolltobototm() {
          var target = document.getElementById("autocomplete");
          var target1 = document.getElementById("mainInput");
          setInterval(function () {
            target.scrollTop = target1.scrollHeight;
          }, 1000);
        }

        function pressAlternatives(direction, event) {
          if (predicted) {
            if (apibusy == true) {
              apibusy = false;
            }
            if (apibusy == false) {
              let wordsArray = cmyInput.value.split(" ");
              let lastW = "";
              if (wordsArray.length > 1) {
                lastW = wordsArray.slice(-1)[0];
              } else {
                lastW = wordsArray[0];
              }

              if (currentSuggestionIndex >= 0) {
                if (direction == "next") {
                  if (currentSuggestionIndex <= currentSuggestions.length) {
                    currentSuggestionIndex += 1;
                  }
                } else {
                  if (currentSuggestionIndex >= 1) {
                    currentSuggestionIndex -= 1;
                  }
                }
              }

              predicted = currentSuggestions[currentSuggestionIndex];
              var new_text = (event.target.value + predicted).replace(
                lastW.toLowerCase(),
                ""
              );
              autocomplete[0].textContent = new_text;
              foundName = new_text;
            }
          } else {
            return;
          }
        }

        function presstabkey() {
          if (predicted) {
            if (apibusy == true) {
              apibusy = false;
            }
            if (apibusy == false) {
              mainInput[0].value = foundName;
              autocomplete[0].textContent = "";
            }
          } else {
            return;
          }
        }

        var autocomplete = document.querySelectorAll("#autocomplete");
        var mainInput = document.querySelectorAll("#mainInput");
        var foundName = "";
        var predicted = "";
        var apibusy = false;
        var mlresponsebusy = false;
        var currentSuggestions = [];
        var currentSuggestionIndex = 0;

        let typingTimer;
        let doneTypingInterval = 100;
        let cmyInput = document.getElementById("mainInput");
        
        cmyInput.addEventListener("keyup", (e) => {
          //check if null value send
          if (mainInput[0].value == "") {
            autocomplete[0].textContent = "";
            return;
          }
          if (e.code == "Space") {
            scrolltobototm();
            return;
          }
          if (
            (e.shiftKey && e.code === "ArrowDown") ||
            (e.shiftKey && e.code === "ArrowUp") ||
            e.code === "ShiftLeft"
          ) {
          } else {
            clearTimeout(typingTimer);
            if (cmyInput.value) {
              let wordsArray = cmyInput.value.split(" ");
              let lastW = "";
              if (wordsArray.length > 1) {
                lastW = wordsArray.slice(-1)[0];
              } else {
                lastW = wordsArray[0];
              }

              typingTimer = setTimeout(doneTyping(e, lastW), doneTypingInterval);
            }

            //check if Backspace key press
            if (e.key == "Backspace") {
              autocomplete[0].textContent = "";
              predicted = "";
              apibusy = true;
              return;
            }

            //check if ArrowRight or Tab key press
            if (e.key != "ArrowRight") {
              if (autocomplete[0].textContent != "" && predicted) {
                var first_character = predicted.charAt(0);
                if (e.key == first_character) {
                  var s1 = predicted;
                  var s2 = s1.substr(1);
                  predicted = s2;
                  apibusy = true;
                } else {
                  autocomplete[0].textContent = "";
                  apibusy = false;
                }
              } else {
                autocomplete[0].textContent = "";
                apibusy = false;
              }
              return;
            } else {
              if (predicted) {
                if (apibusy == true) {
                  apibusy = false;
                }
                if (apibusy == false) {
                  mainInput[0].value = foundName;
                  autocomplete[0].textContent = "";
                }
              } else {
                return;
              }
            }
          }

          function doneTyping(e, lastW) {
            //do something
            CallMLDataSetAPI(e, lastW);
          }

          function CallMLDataSetAPI(event, lastW) {
            apibusy = true;
            queryAPI(lastW).then((val) => {
              currentSuggestions = val.data;
              currentSuggestionIndex = 0;
              apibusy = false;
              if (currentSuggestions && currentSuggestions.length > 0) {
                let term = currentSuggestions[0].replace(lastW.toLowerCase(), "");
                if (term != undefined) {
                  var response = {
                    predicted: term,
                  };
                  if (response.predicted != "") {
                    predicted = response.predicted;
                    var new_text = event.target.value + response.predicted;
                    autocomplete[0].textContent = new_text;
                    foundName = new_text;
                  } else {
                    predicted = "";
                    var new_text1 = event.target.value + predicted;
                    autocomplete[0].textContent = new_text1;
                    foundName = new_text1;
                  }
                }
              }
            });
          }
        });

        document.getElementById("mainInput").addEventListener("keypress", function (e) {
          var sc = 0;
          document.querySelectorAll("#mainInput").forEach(function (element) {
            element.setAttribute("style", "height:" + 0 + "px;overflow-y:hidden;");
            element.setAttribute(
              "style",
              "height:" + (element.scrollHeight + 3) + "px;overflow-y:hidden;"
            );
            sc = element.scrollHeight;
          });
          document.querySelectorAll("#autocomplete").forEach(function (element) {
            if (sc <= 400) {
              element.setAttribute("style", "height:" + 0 + "px;overflow-y:hidden;");
              element.setAttribute(
                "style",
                "height:" + (sc + 2) + "px;overflow-y:hidden;"
              );
            }
            element.addEventListener("input", function (e) {
              e.style.height = 0;
              e.style.height = sc + 2 + "px";
            });
          });
        });

        document.getElementById("mainInput").addEventListener("keydown", function (e) {
          if (e.code === "Tab") {
            e.preventDefault();
            presstabkey();
          }
          if (e.shiftKey && e.code === "ArrowDown") {
            pressAlternatives("next", e);
          } else if (e.shiftKey && e.code === "ArrowUp") {
            pressAlternatives("previous", e);
          }
        });

      });
    });
  },
  methods: {
    onBagClick() {
      let textH = this.getSel("mainInput");
      const isTextHighlighting = textH !== "";
      if (isTextHighlighting) {
        this.highlightedText = textH;
      }
    },
    getSel(id) {
      var txtarea = document.getElementById(id);
      var start = txtarea.selectionStart;
      var finish = txtarea.selectionEnd;
      var sel = txtarea.value.substring(start, finish);
      return sel;
    },
  },
  computed: {},
};
</script>
