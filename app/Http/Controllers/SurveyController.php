<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Models\Survey;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
          $user = $request->user();
          return SurveyResource::collection(Survey::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(9));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();
        // check if image was given and save on local file system
        if(isset($data['image'])){
            $relative_path = $this->saveImage($data['image']);
            $data['image'] = $relative_path;
        }
        $survey = Survey::create($data);

        // create new questions
        foreach($data['questions'] as $question){
               $question['survey_id'] = $survey->id;
               $this->createQuestion($question);
        }

        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey, Request $request)
    {
         $user = $request->user();
         if($user->id !== $survey->user_id){
            return abort(403, 'Unauthorized action.');
         }

         return new SurveyResource($survey);
    }

    public function show_for_guest(Survey $survey){
          return new SurveyResource($survey);
    }

    // store/save questions answers
    public function store_answer(StoreSurveyAnswerRequest $request, Survey $survey){
             $validated = $request->validated();
             
             $survey_answer = SurveyAnswer::create([
                   'survey_id' => $survey->id,
                   'start_date' => date('Y-m-d H:i:s'),
                   'end_date' => date('Y-m-d H:i:s'),
             ]);

             foreach($validated['answers'] as $question_id => $answer){
                 $question = SurveyQuestion::where(['id' => $question_id, 'survey_id' => $survey->id])->get();
                 if(!$question){
                      return response('Invalid question ID: "$question_id"', 400);
                 }

                 $data = [
                        'survey_question_id' =>  $question_id,
                        'survey_answer_id' => $survey_answer->id,
                        'answer' => is_array($answer) ? json_encode($answer) : $answer  
                 ];

                 SurveyQuestionAnswer::create($data);
            } 
            
            return response($content = "", 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyRequest  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();
         // check if image was given and save on local file system
         if(isset($data['image'])){
            $relative_path = $this->saveImage($data['image']);
            $data['image'] = $relative_path;

            // if image exists, then delete it
            if($survey->image){
                 $absolute_path = public_path($survey->image);
                 File::delete($absolute_path);
            }
        }

        // update survey
        $survey->update($data);

        // update survey questions
        // get ids as plain array of existing questions
        $existing_ids = $survey->questions()->pluck('id')->toArray();
        // get ids as plain array of new questions
        $new_ids = Arr::pluck($data['questions'], 'id');
        // find question to delete
        $to_delete = array_diff($existing_ids, $new_ids);
        // find question to add
        $to_add = array_diff($new_ids, $existing_ids);
        // delete question by $to_delete array
        SurveyQuestion::destroy($to_delete);
        // create new questions
        foreach($data['questions'] as $question){
              if(in_array($question['id'], $to_add)){
                 $question['survey_id'] = $survey->id;
                 $this->createQuestion($question);
              }
        }
        // update existing questions
        $question_map = collect($data['questions'])->keyBy('id');
        foreach($survey->questions as $question){
              if(isset($question_map[$question->id])){
                 $this->updateQuestion($question, $question_map[$question->id]);
              }
        }
        return new SurveyResource($survey);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();
        if($user->id !== $survey->user_id){
           return abort(403, 'Unauthorized action.');
        }
        $survey->delete();
         // if image exists, then delete it
         if($survey->image){
            $absolute_path = public_path($survey->image);
            File::delete($absolute_path);
       }
        return response($content = '', 204);
    }

    // image upload method
    private function saveImage($image){
          // check if image is valid base64 string
          if(preg_match('/^data:image\/(\w+);base64,/', $image, $type)){
                // take out the base64 encoded text without mime type
                $image = substr($image, strpos($image, ',') + 1);
                // get file extension
                $type = strtolower($type[1]); // jpg, png, gif
                // check if file is on image
                if(!in_array($type,['jpg', 'jpeg', 'gif', 'png', 'webp'])){
                throw new \Exception('Invalid image type.');
                }
                $image = str_replace(' ', '+', $image);
                $image = base64_decode(($image));
                if($image === false){
                    throw new \Exception('base64_decode failed.');  
                }
          }else{
             throw new \Exception('Did not match data URI with image data.');
          }
          $dir = 'images/';
          $file = Str::random().'.'.$type;
          $absolute_path = public_path($dir);
          $relative_path = $dir.$file;

          if(!File::exists($absolute_path)){
              File::makeDirectory($absolute_path, 0755, true);
          }
          file_put_contents($relative_path, $image);
          return $relative_path;
    }

    // question store/create method
    private function createQuestion($data){
         if(is_array($data['data'])){
              $data['data'] = json_encode($data['data']);
         }

         $validator = Validator::make($data, [
             'question' => 'required|string',
             'type' => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
             ])],
             'description' => 'nullable|string',
             'data' => 'present',
             'survey_id' => 'exists:App\Models\Survey,id'
         ]);

         return SurveyQuestion::create($validator->validated());
    }
    // question update method
    private function updateQuestion(SurveyQuestion $question,$data){
         if(is_array($data['data'])){
              $data['data'] = json_encode($data['data']);
         }
         $validator = Validator::make($data, [
            'id' => 'exists:App\Models\SurveyQuestion,id',
             'question' => 'required|string',
             'type' => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
             ])],
             'description' => 'nullable|string',
             'data' => 'present',
         ]);
         return $question->update($validator->validated());
    }
}
