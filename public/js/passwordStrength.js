(function ($) {
    $.fn.zxcvbnProgressBar = function (options) {
        //init settings
        var settings = $.extend(
                {
                    passwordInput: "#passwordInput",
                    userInputs: [],
                    ratings: ["Very weak", "Weak", "OK", "Strong", "Very strong"],
                    allProgressBarClasses:
                            "bg-danger bg-warning bg-success progress-bar-striped progress-bar-animated",
                    progressBarClass0:
                            "bg-danger progress-bar-striped progress-bar-animated",
                    progressBarClass1:
                            "bg-danger progress-bar-striped progress-bar-animated",
                    progressBarClass2:
                            "bg-warning progress-bar-striped progress-bar-animated",
                    progressBarClass3: "bg-success progress-bar-striped",
                    progressBarClass4: "bg-success progress-bar-striped"
                },
                options
                );

        return this.each(function () {
            settings.progressBar = this;
            UpdateProgressBar();
            $(settings.passwordInput).keyup(function (event) {
                UpdateProgressBar();
            });
        });

        function UpdateProgressBar() {
            var progressBar = settings.progressBar;
            var password = $(settings.passwordInput).val();
            if (password) {
                var result = zxcvbn(password, settings.userInputs);
                var scorePercentage = (result.score + 1) * 20;
                $(progressBar).css("width", scorePercentage + "%");

                if (result.score == 0) {
                    //weak
                    $(progressBar)
                            .removeClass(settings.allProgressBarClasses)
                            .addClass(settings.progressBarClass0);
                    $(progressBar).html(settings.ratings[0]);
                } else if (result.score == 1) {
                    //normal
                    $(progressBar)
                            .removeClass(settings.allProgressBarClasses)
                            .addClass(settings.progressBarClass1);
                    $(progressBar).html(settings.ratings[1]);
                } else if (result.score == 2) {
                    //medium
                    $(progressBar)
                            .removeClass(settings.allProgressBarClasses)
                            .addClass(settings.progressBarClass2);
                    $(progressBar).html(settings.ratings[2]);
                } else if (result.score == 3) {
                    //strong
                    $(progressBar)
                            .removeClass(settings.allProgressBarClasses)
                            .addClass(settings.progressBarClass3);
                    $(progressBar).html(settings.ratings[3]);
                } else if (result.score == 4) {
                    //very strong
                    $(progressBar)
                            .removeClass(settings.allProgressBarClasses)
                            .addClass(settings.progressBarClass4);
                    $(progressBar).html(settings.ratings[4]);
                }
            } else {
                $(progressBar).css("width", "0%");
                $(progressBar)
                        .removeClass(settings.allProgressBarClasses)
                        .addClass(settings.progressBarClass0);
                $(progressBar).html("");
            }
        }
    };
})(jQuery);
