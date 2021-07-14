
jQuery(document).ready(function() {
    
    if (window.location.hash == '#questionnaire') {
        jQuery(".account-tabs-menu li > a").trigger('click');
    } else {
        jQuery('#questionnaire').hide();
    }
    initQuestionaryForm();
    //
    // jQuery('#questionnaire').load("investor-questionnaire", {}, function() {
    //     loadQuestionaryContent()
    // });
    
    /**
     *
     */
    function loadQuestionaryContent()
    {
        
        jQuery('#questionnaire').addClass("ajax-loader");
        jQuery(".survey-submit").attr("disabled", "disabled");
        
        jQuery.ajax({
                        type: "POST",
                        url: "/investor-questionnaire/loadData",
                        data: jQuery('form.surveys').serializeArray(),
                        dataType: 'json'
                    }).done(function(data) {
            
            jQuery('#questionnaire').removeClass("ajax-loader");
            
            if (data.content) {
                jQuery('#questionnaire-content').html(data.content);
            }
            
            if (data.errorMessage) {
                noty({
                         text: data.errorMessage,
                         type: 'error',
                         theme: 'message-block',
                         layout : 'topRightWordWrap'
                     });
            }
            
            initQuestionaryForm(); //reinit
        });
        
    }
    
    /**
     *
     */
    function initQuestionaryForm()
    {
        var submitted = false;
        jQuery("form.surveys .survey-submit").click(function(event){
            event.preventDefault();
            
            jQuery("form.surveys .ajax-loader").removeClass('hidden');
            
            jQuery(".survey-submit").attr("disabled", "disabled");
            if (jQuery(this).hasClass('continue')) {
                jQuery('#pageAction').val('next');
            } else if (jQuery(this).hasClass('back')) {
                jQuery('#pageAction').val('prev');
            } else if (jQuery(this).hasClass('submit')) {
                jQuery('#pageAction').val('submit');
            }
            
            jQuery('form.surveys').submit();
            //    loadQuestionaryContent();
        });
        jQuery('form.surveys').submit(function(event){
            
            if (submitted) {
                return false;
            }
            
            submitted = true;
            return true;
        });
        
        
        jQuery('.risk-radio-btn-parent input').on('change', function() {
            checkRiskSub();
        });
        checkRiskSub();
        
        jQuery('.hide-radio-btn').each(function() {
            var radioWithoutCheck = jQuery('.hide-radio-btn').find('input[type="radio"]');
            if (radioWithoutCheck.length == 1) {
                radioWithoutCheck.attr('checked', 'checked');
                radioWithoutCheck.trigger( "change" );
            }
        });
        
    }
    
    function isAnswered(answer) {
        var answerValueElements = answer.children('input,select');
        if (answerValueElements.length <= 0) {
            return false;
        }
        
        var isAnswered = false;
        
        answerValueElements.each(function () {
            
            if (
                jQuery(this).prop("tagName") === 'INPUT'
                && (
                    jQuery(this).attr('type') === 'radio'
                    || jQuery(this).attr('type') === 'checkbox'
                )
            ) {
                
                isAnswered = jQuery(this).is(':checked');
                
            } else {
                
                isAnswered = !!jQuery(this).val();
                
            }
            
            return;
            
        });
        
        
        return isAnswered;
    }
    
    function initSubQuestion(question) {
        
        question.parents(".answer").each(function(){
            
            var parentAnswer = jQuery(this);
            
            if (isAnswered(parentAnswer)) {
                
                question.children('.child-elements').children('.answer').children('input').removeAttr('disabled');
                question.children('.select-element').children('.answer').children('select').removeAttr('disabled');
                question.removeClass('hidden');
                
            } else {
                
                question.children('.child-elements').children('.answer').children('input').attr('disabled', 'disabled');
                question.children('.select-element').children('.answer').children('select').attr('disabled', 'disabled');
                
                if (!question.hasClass('hidden')) {
                    question.addClass('hidden');
                }
                
            }
            
        });
        
    }
    
    function initCustomAnswers(question) {
        var allQuestionsAnswered = true;
        
        question.parents(".answer").each(function(){
            var parentAnswer = jQuery(this);
            
            allQuestionsAnswered = isAnswered(parentAnswer);
            if (!allQuestionsAnswered) {
                return false; //break loop if at least one without answer
            }
        });
        
        if (allQuestionsAnswered) {
            question.removeAttr('disabled');
        } else {
            question.attr('disabled', 'disabled');
        }
    }
    
    var dependencies = {};
    function getDependencies(question) {
        
        var dependsOn = question.data('depends');
        if (!dependsOn) {
            return;
        }
        for (key in dependsOn) {
            var dependency = dependsOn[key];
            if (
                typeof dependency.question !== 'undefined'
                && typeof dependency.answer !== 'undefined'
            ) {
                dependencies[dependency.question] = dependencies[dependency.question] || {};
                dependencies[dependency.question][dependency.answer] = dependencies[dependency.question][dependency.answer] || [];
                dependencies[dependency.question][dependency.answer].push(question.attr('id'));
            }
            
        }
        
    }
    
    function initDependencies(changedAnswer) {
        
        if (
            changedAnswer.prop("tagName") === 'INPUT'
            && changedAnswer.attr('type') === 'radio'
        ) {
            
            var questionsHolder = changedAnswer.parent('.answer').parent('.child-elements,.select-element');
            var question = questionsHolder.parent('.question');
            
            var questionId = question.data('id');
            
            questionsHolder.children('.answer').each(function () {
                
                var customFileds = jQuery(this).children('.custom-field').children('input,select');
                
                customFileds.each(function () {
                    initCustomAnswers(jQuery(this));
                });
                
                var subQuestions = jQuery(this).children('.child-elements,.select-element').children('.question');
                
                subQuestions.each(function () {
                    initSubQuestion(jQuery(this));
                });
                
                changeByDependencies(questionId, jQuery(this).data('id'));
                
            });
            
        } else {
            
            var customFileds = changedAnswer.parent().children('.custom-field').children('input,select');
            
            customFileds.each(function () {
                initCustomAnswers(jQuery(this));
            });
            
            var subQuestions = changedAnswer.parent().children('.child-elements,.select-element').find('input,select');
            
            subQuestions.each(function () {
                initSubQuestion(jQuery(this));
            });
            
        }
        
    }
    
    function changeByDependencies(questionId, answerId, value) {
        
        if (
            questionId
            && typeof dependencies[questionId] !== 'undefined'
        ) {
            
            var allAnswersForQuestion = dependencies[questionId];
            if (!jQuery.isEmptyObject(allAnswersForQuestion)) {
                
                
                var neededAnswers = dependencies[questionId];
                
                if (
                    answerId && neededAnswers[answerId]
                    && typeof neededAnswers[answerId] !== 'undefined'
                    && neededAnswers[answerId].length > 0
                ) {
                    checkAllDependencies();
                }
            }
            
        }
    }
    
    function checkAllDependencies() {
        
        var showdDependentElements = [];
        
        jQuery.each(dependencies, function (questionId, answerData) {
            
            jQuery.each(answerData, function (answerId, dependentElements) {
                
                if (dependentElements.length > 0) {
                    
                    var value = jQuery('.question[data-id="' + questionId + '"] .answer[data-id="' + answerId + '"]').children('input').is(':checked');
                    
                    for (answerKey in dependentElements) {
                        if (showdDependentElements.indexOf(dependentElements[answerKey]) > -1) {
                            continue;
                        }
                        
                        var dependentQuestion = jQuery('#' + dependentElements[answerKey]);
                        
                        if (value) {
                            
                            showdDependentElements.push(dependentElements[answerKey]);
                            
                            dependentQuestion.children('.child-elements').children('input').removeAttr('disabled');
                            dependentQuestion.children('.select-element').children('select').removeAttr('disabled');
                            dependentQuestion.removeClass('hidden');
                            
                        } else {
                            
                            dependentQuestion.children('.child-elements').children('input').attr('disabled', 'disabled');
                            dependentQuestion.children('.select-element').children('select').attr('disabled', 'disabled');
                            
                            if (!dependentQuestion.hasClass('hidden')) {
                                dependentQuestion.addClass('hidden');
                            }
                            
                        }
                        
                    }
                }
            });
            
        });
        
    }
    
    
    function checkRiskSub() {
        if (jQuery('.risk-radio-btn input').prop("checked")) {
            jQuery('.risk-disclaimer').show();
        } else {
            jQuery('.risk-disclaimer input').attr('checked', 'checked');
            jQuery('.risk-disclaimer').hide();
        }
    }
    
    jQuery(".question,.list").each(function(){
        getDependencies(jQuery(this));
    });
    
    checkAllDependencies();
    
    jQuery(".answer > input, .answer > select").change(function(){
        initDependencies(jQuery(this))
    });
    
    jQuery(".answer .question").each(function(){
        initSubQuestion(jQuery(this));
    });
    
    jQuery(".answer > .custom-field > input").each(function(){
        initCustomAnswers(jQuery(this));
    });
    
});