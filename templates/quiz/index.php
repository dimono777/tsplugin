<?php

use tradersoft\helpers\Arr;

/** @var array $templateVariables */
$templateVariables = TSInit::$app->getVar('templateVariables', []);

/** @var array $fullData */
$fullData = Arr::get($templateVariables, 'fullData', []);

/** @var string $ajaxUrl */
$ajaxUrl = Arr::get($templateVariables, 'ajaxUrl', '/');

/** @var string $defaultError */
$defaultError = Arr::get($templateVariables, 'defaultError');

?>

<!-- quiz html here -->

<form id="js-quizModule" class="quizModule" method="post" @submit="onSubmit" >
    <div class="quizModule__title" v-html="json.title | parseBB"></div>
    <div class="quizModule__subtitle" v-html="json.description | parseBB"></div>

    <div v-for="section in json.children | orderBy 'position'" class="quizSection">
        <div class="quizSection__title" v-html="section.title"></div>
        <div class="quizSection__description" v-html="section.description | parseBB"></div>

        <div v-for="block in section.children | orderBy 'position'" class="quizBlock">
            <div class='quizBlock__title' v-html="block.title | parseBB"></div>

            <template v-for="question in block.children | orderBy 'position'">

                <div v-if="question.questionTypeName == 'Checkbox'" class="quizQuestion__answerLine" >
                    <checkbox-group :question="question" :server-validation="serverValidation"></checkbox-group>
                </div>

                <div v-if="question.questionTypeName == 'Radio'" class="quizQuestion__answerLine" >
                    <radio-group :question="question" :server-validation="serverValidation"></radio-group>
                </div>

            </template>
        </div>
    </div>

    <button type="submit" id="js-quizModule__btnSubmit" class="quizModule__btnSubmit">Submit</button>
    <div class="quizQuestion__error_big">{{ validationError }}</div>
</form>

<script type="text/x-template" id="checkbox-group-template">
    <validator  name="validation">
        <div class="quizQuestion" :class="{ 'quizQuestion_hasError': hasError }">
            <div class='quizQuestion__title'>
                <span class="quizQuestion__titleNum">{{question.position}}. </span>
                <span v-html="question.title | parseBB"></span>
                <span v-show="question.isMandatory == 1" class="quizQuestion__mandatory">*</span>
            </div>

            <div class="quizQuestion__answer" v-for="answer in question.children | orderBy 'position'">
                <label class='quizQuestion__answerLabel'>
                    <input
                            type='checkbox'
                            class="quizQuestion__answerInput"
                            name='results[{{answer.questionId}}][{{answer.id}}]'
                            value='{{answer.id}}'
                            v-validate:question="questionValidation"
                            v-model="selectedValue"
                    />
                    <span v-html="answer.title | parseBB"></span>
                </label>
            </div>

            <template v-for="msg in $validation.question.errors">
                <p class='quizQuestion__error'>{{msg.message}}</p>
            </template>
        </div>
    </validator>
</script>

<script type="text/x-template" id="radio-group-template">
    <validator name="validation">
        <div class="quizQuestion" :class="{ 'quizQuestion_hasError': hasError }">
            <div class='quizQuestion__title'>
                <span class="quizQuestion__titleNum">{{question.position}}. </span>
                <span v-html="question.title | parseBB"></span>
                <span v-show="question.isMandatory == 1" class="quizQuestion__mandatory">*</span>
            </div>

            <div class="quizQuestion__answer" v-for="answer in question.children | orderBy 'position'">

                <label class='quizQuestion__answerLabel'>
                    <input
                            type='radio'
                            class="quizQuestion__answerInput"
                            name='results[{{answer.questionId}}][{{selectedValue}}]'
                            value='{{answer.id}}'
                            v-validate:question="questionValidation"
                            v-model="selectedValue"
                    />
                    <span v-html="answer.title | parseBB"></span>
                </label>
            </div>
            <template v-for="msg in errorMessages">
                <p class='quizQuestion__error'>{{msg.message}}</p>
            </template>
        </div>
    </validator>
</script>
<script>
    var GLOBAL = GLOBAL || {};

    GLOBAL.pageInfo = {
        id: 'quiz'
    };

    var quizTree = <?php echo json_encode($fullData); ?>,
        ajaxUrl = '<?php echo $ajaxUrl; ?>',
        defaultErrorMsg = '<?php echo $defaultError; ?>';

    Vue.use(VueValidator);

    var validationMixin = {
        props: ['question', 'serverValidation'],
        data: function() {
            return {
                selectedValue: null,
                validation: {
                    defaultRule: '',
                    requiredRule: {
                        required: {
                            rule: true,
                            message: 'please, choose an option',
                            initial: 'off'
                        }
                    }
                }
            }
        },
        computed: {
            hasServerError: function() {
                return this.serverValidation
                    && this.serverValidation.hasOwnProperty('questionsIds')
                    && this.serverValidation.questionsIds.indexOf(this.question.id) !== -1;
            },
            hasError: function() {
                if (this.hasServerError) {
                    return true;
                }
                return this.$validation.invalid;
            },
            errorMessages: function() {
                if (this.hasServerError) {
                    return [{
                        message: 'please, choose an option'
                    }];
                }

                return this.$validation.question.errors;
            },
            questionValidation: function () {
                var required = Boolean(parseInt(this.question.isMandatory));

                if (required === true) {
                    return this.validation.requiredRule;
                } else {
                    return this.validation.defaultRule;
                }
            }
        },
        watch: {
            selectedValue: function() {
                this.serverValidation = null;
            }
        }
    };

    Vue.component('checkboxGroup', {
        template: '#checkbox-group-template',
        mixins: [validationMixin] // don't use extends because of it doesn't work with vue-validation plugin and we can't extend props and data which are necessary
    });

    Vue.component('radioGroup', {
        template: '#radio-group-template',
        mixins: [validationMixin]
    });

    Vue.filter('parseBB', function(unparsedString){
            var Parser = function (string){

                string = string || "";

                for (var tag in Parser.tags){
                    var regTag = tag.replace(/[\[\]]/g , '\\$&');
                    var regExp = new RegExp(regTag, 'gi');

                    string = string.replace(regExp, Parser.tags[tag]);
                }

                return string;
            };

            Parser.tags = {
                '\[b\](.+?)\[\/b\]': '<b>$1</b>',
                '\[u\](.+?)\[\/u\]': '<u>$1</u>',
                '\[i\](.+?)\[\/i\]': '<i>$1</i>',
                "[url=(.+?)](.*?)[/url]": function (match, href, body) {
                    return (body || href).link(href);
                }
            };

            var parsedString = Parser(unparsedString);
            return parsedString;
        });

    var app = (function($) {
      return new Vue(
          {
            el: '#js-quizModule',
            data: {
              json: quizTree,
              ajaxUrl: ajaxUrl,
              defaultErrorMsg: defaultErrorMsg,
              serverValidation: null,
              validationError: null,
            },
            methods: {
              validate: function() {
                var valid = true,
                    invalidQuestions = [];
            
                this.$children.forEach(function(child) {
                  if (child.hasOwnProperty('$validation')) {
                    child.$validate();
                    if (child.$validation.invalid) {
                      valid = false;
                      invalidQuestions.push(child.question.position);
                    }
                  }
                });
            
                if (!valid) {
                  this.validationError = 'Please, fill these questions: ' + invalidQuestions.join(', ');
                }
            
                return valid;
              },
              getPositionsByQuestionsIds: function(questionIds) {
                var positions = [];
                for (var sectionId in app.json.children) {
                  var section = app.json.children[sectionId];
              
                  for (var blockId in section.children) {
                    var block = section.children[blockId];
                
                    for (var questionId in block.children) {
                      var question = block.children[questionId];
                  
                      if (question && questionIds.indexOf(question.id) !== -1) {
                        positions.push(question.position);
                      }
                  
                    }
                  }
                }
            
                return positions;
              },
              onSubmit: function(e) {
                var self = this;
            
                if (this.validate()) {
                  $.ajax(
                      {
                        url: self.ajaxUrl,
                        method: 'POST',
                        data: $('#js-quizModule').serialize(),
                        dataType: 'json',
                        beforeSend: function() {
                          $('#js-quizModule__btnSubmit').before('<div class="quizModule__loader"></div>');
                          $('.quizSection').prepend('<div class="quizSection__overlay"></div>');
                          $('#js-quizModule__btnSubmit').hide();
                        },
                        success: function(result) {
                          if (!result || !result.hasOwnProperty('success')) {
                            self.validationError = self.defaultErrorMsg;
                            return false;
                          }
                      
                          if (result.success) {
                        
                            window.location.reload();
                        
                          }
                          else {
                        
                            self.serverValidation = result.validationErrors || {};
                        
                            if (
                                self.serverValidation.hasOwnProperty('questionsIds')
                            ) {
                          
                              self.validationError = 'Please, fill these questions: '
                                                     + self.getPositionsByQuestionsIds(
                                      result.validationErrors.questionsIds,
                                  ).join(', ');
                          
                            }
                            else {
                              self.validationError = result.message || self.defaultErrorMsg;
                            }
                          }
                        },
                        error: function(result) {
                          $('.quizModule__loader').remove();
                          $('.quizSection__overlay').remove();
                          $('#js-quizModule__btnSubmit').show();
                          self.validationError = self.defaultErrorMsg;
                        },
                        complete: function() {
                          $('.quizModule__loader').remove();
                          $('.quizSection__overlay').remove();
                          $('#js-quizModule__btnSubmit').show();
                        },
                      });
                }
            
                e.preventDefault();
                return false;
              },
            },
          },
      );
    })(window.jQuery);

</script>