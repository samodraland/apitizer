<?php

/**
 * This is a simple usage example.
 * Your request methods (get, post, put, patch, delete) are defined as functions
 * 
 * Just in this example, each endpoint uses the query string parameter "?format=" just to demonstrate different outputs 
 * to the available formats: JSON, JSONP, XML & HTML template. The default output is JSON if there's no "?format=" in the url.
 * If there's jsonpcallback query string in url ("?jsonpcallback=your_callback_name") then it outputs as JSONP.
 * In the real code choose the desired output format.
 * 
 * You can create parameter schema by calling Utils::constructSchema() to collect all parameters automatically
 * or you can construct parameter schema manually:
 * $schema = array(
 *  ":key1" => "value1",
 *  ":key2" => "value2",
 * );
 * 
 * All callback values are represented as $callbackvalues on each route.
 * 
 * For complete documentation please refer here: https://github.com/samodraland/apitizer
 * For dummy data reference please refer to the exported SQL file in the repository
 * 
 */

class Employees extends Controller{

    private $model = null;

    function __construct(){
        $this->model = new EmployeeModel();
    }

    public function get(){
        $this->map(array(
            /**
             * example without parameter
             * url: http://yourdomain/employees
             */
            "/" => function( $callbackvalues ){
                $result = $this->model->getAllEmployees();
                $format = $callbackvalues["queries"]["format"];
                if ($format == "xml"){                    
                    return $this->xml($result);
                } else if ($format == "html"){
                    return $this->html("web-page/view-employees",$result["result"]);
                } else {                    
                    return $this->json($result);
                }
            },

            /**
             * example with 1 parameter
             * url: http://yourdomain/employees/10
             */
            "/:id" => function( $callbackvalues ){
                // construct schema manually
                $schema = array(
                    ":id" => Utils::validateString( $callbackvalues["keys"]["id"] )
                );
                $result = $this->model->getEmployeeById( $schema );
                $format = $callbackvalues["queries"]["format"];
                if ($format == "xml"){
                    return $this->xml($result);
                } else if ($format == "html"){
                    return $this->html("web-page/view-employees",$result["result"]);
                } else {
                    return $this->json($result);
                }
            },

            /**
             * example with more specific url with 1 parameter placeholder and query string for pagination
             * url: http://yourdomain/employees/status/the_status?page=1
             */
            "/status/:status" => function( $callbackvalues ){
                $page = empty($callbackvalues["queries"]) ? 0 : $callbackvalues["queries"]["page"];
                
                // construct schema automatically
                $schema = Utils::constructSchema($callbackvalues["keys"]);
                
                /**
                 * We're calling Utils::constructSchema() to create formated SQL parameter automatically, 
                 * its result will be carried as parameter array to SQL query in the model file.
                 * Since there is no placeholder for ":format" parameter in the SQL query, 
                 * it has to be removed from schema using unset() to avoid PDO "invalid parameter number" error
                 */
                
                unset($schema[":format"]);

                // after removed parameter can be passed to the model
                $result = $this->model->getByStatus( $schema, $page * 10 );
                $format = $callbackvalues["queries"]["format"];
                if ($format == "xml"){
                    return $this->xml($result);
                } else if ($format == "html"){
                    return $this->html("web-page/view-employees",$result["result"]);
                } else {
                    return $this->json($result);
                }
            },

            /**
             * example with more specific url and more than 1 parameter placeholders
             * url: http://yourdomain/search/the_keyword/status/the_status
             */
            "/search/:keyword/status/:status" => function( $callbackvalues ){
                // construct schema automatically
                $schema = Utils::constructSchema($callbackvalues["keys"]);
                $result = $this->model->searchEmployee( $schema );
                unset($schema[":format"]);
                $format = $callbackvalues["queries"]["format"];
                if ($format == "xml"){
                    return $this->xml($result);
                } else if ($format == "html"){
                    return $this->html("web-page/view-employees",$result["result"]);
                } else {
                    return $this->json($result);
                }
            },

            "/email/:id" => function( $callbackvalues ){
                /**
                 * This endpoint is to send email using PHPMailer library.
                 * If you test this on local mail server please setup an email account on your local mail server.
                 * Change email address from one of the employees data to your email account.
                 * The email template source can be viewed under /view/email-templaye/theme1
                 */
                $schema = array(
                    ":id" => Utils::validateString( $callbackvalues["keys"]["id"] )
                );
                $record = $this->model->getEmployeeById( $schema );
                $templateString = Utils::renderTemplate("/email-template/theme1/index", array(
                    "FullName" => $record["result"][0]["FirstName"] . " " . $record["result"][0]["LastName"]
                ));
                $mail = new Mailer(); 
                $mailResult = $mail->sendMail(
                    $record["result"][0]["Email"], 
                    $record["result"][0]["FirstName"],
                    "Email testing",
                    $templateString,
                    array(
                        array(
                            "cid" => "mytheme1cid",
                            "image" => Properties::getProperties("email")["embedimage"]."/theme1/images/header.jpg"
                        )
                    )
                );
                if ($mailResult["response"] == 200){
                    $mailResult["message"] = "Email has been sent";
                    $mailResult["result"] = $record["result"];
                    return $this->json($mailResult);
                }else{
                    return $this->json($mailResult);
                }                
            }

        ));
    }

    public function delete(){
        $this->map(array(
            "/:id" => function( $callbackvalues ){
                return;
                // construct schema automatically
                $schema = Utils::constructSchema($callbackvalues["keys"]);
                // check if id exists
                $check = $this->model->getEmployeeById( $schema );
                if (!empty($check["result"])){
                    $result = $this->model->deleteEmployee( $schema );
                    return $this->json($result);
                }else{
                    die(Utils::response(404) );
                }                
            }
        ));
    }

    /**
     * the parameters should be the same as in parameter placeholder in SQL model file.
     * There are :first, :last, :email
     */
    public function post(){
        $this->map(array(
            "/" => function( $callbackvalues ){
                // construct schema automatically
                $schema = Utils::constructSchema($callbackvalues["data"]);
                $result = $this->model->newEmployee( $schema );
                return $this->json($result);
            }
        ));
    }

    public function put(){
        $this->map(array(
            "/:id" => function( $callbackvalues ){
                // construct schema automatically
                $id = Utils::constructSchema($callbackvalues["keys"]);
                // check if id exists
                $check = $this->model->getEmployeeById( $id );
                if (!empty($check["result"])){
                    // construct schema manually
                    $schema = array(
                        ":first" => Utils::validateString( $callbackvalues["data"]["first"] ),
                        ":last" => Utils::validateString( $callbackvalues["data"]["last"] ),
                        ":email" => Utils::validateString( $callbackvalues["data"]["email"] ),
                        ":id" => Utils::validateString( $callbackvalues["keys"]["id"] )
                    );
                    $result = $this->model->updateEmployee( $schema );
                    return $this->json($result);
                }else{
                    die(Utils::response(404) );
                }
            }
        ));
    }    
}

?>