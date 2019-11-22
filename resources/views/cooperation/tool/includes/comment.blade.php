<?php
    $helpId = time();
    $currentInputSource = \App\Helpers\HoomdossierSession::getInputSource(true);
    // set some default to prevent isset spaghetti stuff.
    $currentInputSourceHasNoPlacedComment = false;
    $currentInputSourceHasACommentButIsEmpty = true;
    $commentsForCurrentStep = [];
    $columnName = isset($short) ? "step_comments[comment][{$short}]" : "step_comments[comment]";

    // obtain the comments for the current step, when its a substep, the comment will be stored in the substep
    // else get it from the main step
    $subStepShort = $currentSubStep->short ?? '-';
    // make sure the steps / keys exist before proceeding
    if (array_key_exists($currentStep->short, $commentsByStep) && array_key_exists($subStepShort, $commentsByStep[$currentStep->short])) {

        $commentsForCurrentStep = $commentsByStep[$currentStep->short][$currentSubStep->short ?? '-'];
        if (isset($short)) {

            $currentInputSourceHasNoPlacedComment = !isset($commentsForCurrentStep[$currentInputSource->name][$short]);

            $currentInputSourceHasACommentButIsEmpty = empty($commentsForCurrentStep[$currentInputSource->name][$short]);
        } else {
            $currentInputSourceHasNoPlacedComment = !isset($commentsForCurrentStep[$currentInputSource->name]);
            $currentInputSourceHasACommentButIsEmpty = empty($commentsForCurrentStep[$currentInputSource->name]);
        }
    }

?>
@if(!empty($commentsForCurrentStep))
@foreach($commentsForCurrentStep as $inputSourceName => $comment)
        {{--a nice uitzondering op de regel for only one case--}}
        @if(is_array($comment))
            <?php $comment = $comment[$short]; ?>
        @endif

        {{--
            Its possible a comment is stored, but is empty.
            We dont want to show that to the user
         --}}
        @if(!empty($comment))
        <div class="row">
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question', ['id' => null, 'translation' => $translation])
                    @if($currentInputSource->name != $inputSourceName)({{$inputSourceName}}) @endif

                    @if($inputSourceName === $currentInputSource->name)
                        <textarea name="{{$columnName}}" class="form-control">{{old($columnName, $comment)}}</textarea>
                    @else
                        <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                    @endif
                @endcomponent

            </div>
        </div>
        @endif
    @endforeach
@endif
@if($currentInputSourceHasACommentButIsEmpty || $currentInputSourceHasNoPlacedComment)
<div class="row">
    <div class="col-sm-12">
        @component('cooperation.tool.components.step-question', ['id' => 'step_comments.comment', 'translation' => $translation])
            <textarea name="{{$columnName}}" class="form-control">{{old($columnName)}}</textarea>
        @endcomponent
    </div>
</div>
@endif
