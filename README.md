<img src="https://raw.githubusercontent.com/samodraland/apitizer/refs/heads/main/assets/apitizer.png" alt="drawing" width="300"/>
<br/>

## APITIZER

As the name suggests, which is a play on the word appetizer, this framework is a simple structure & small code with minimal configuration that is ready to be used as an appetizer for your code as the main course.

## History

Most of my projects are small to medium scale and need to be completed quickly. Therefore, I don’t have much time to try and use the existing frameworks out there for the backend side of my projects, but I always use framework for the frontend side since most of the time I work as a Frontend Developer. I made this simple framework to suit what I need for my projects. Now I decided to open source it. Maybe this framework will work for you too.

If you are willing to contribute please fork it to your own repo and create PR/MR when you are going to merge.

## What is Apitizer?

Apitizer is a framework to generate JSON/JSONP, XML and simple HTML template rendering.

## Documentation

### Requirements

1.  PHP 7.0 and latest
2.  PDO
3.  Zlib Functions enabled

### Instalation

1. Fork this repo to your own repo or download as zip file and extract to your PHP web server.
2. Adjust the value of `RewriteBase` line in the `.htaccess` file and point to your Apitizer working directory.

   ```
    RewriteBase /yourworkingdirectory
   ```

3. This repo includes example of controller, model & sql file for you to test. Create new MySQL DB and execute apitizer.sql.
4. do `composer install`

### Structures

Apitizer applies MVC design pattern with OOP. There is no specific router to handle the endpoint url since the endpoint name and its handler is the controller name.

Main directory:

- `core`: This directory is where the main codes are that make this framework works.

Working directory:

- `controller`: directory for your controllers as the handler of the router endoint. 
- `model`: directory for your model files that contain SQL queries.
- `helper`: directory for Apitizer default library or your own library.
- `static`: directory for static content for configurations or middlewares list.
- `vendor`: directory for 3rd party libraries.
- `view`: directory for your template files.

### Manual

**1. Configuration**
<hr/>

  Default configurations are located inside static/properties.php. You can change/add your custom configuration.

  **Global config**
  
  | Config | Type | Description |
  | :- | :- | :- |
  | `app["lctime"]` | String | Local region information. |
  | `app["timezone"]` | String | Your app time zone. List can be seen here: [https://www.php.net/manual/en/timezones.php](https://www.php.net/manual/en/timezones.php) |
  | `app["supportHtaccess"]` | Boolean | htaccess support in your server. Setting true or false whether your server doesn't support htaccess or you don't use htaccess |
  | `env["dev"]` | String | url path to your development environment |
  | `env["staging"]` | String | url path to your staging/UAT environment |
  | `env["prod"]` | String | url path to your production environment |

  Calling configuration:
  ```
  Properties::getProperties($key, $subkey = null): array | string
  ```  
  ex: `Properties::getProperties("app", "timezone")`


  **Custom configuration**

  You can add your own custom configuration by making two dimensional array and save it under `static` directory. The file name & the array variable name have to be the same.

  Example:

  `static/mycustomconfig.php`

  ```
    $mycustomconfig = array(
      "parent1" => array(
        "key1" => "value1",
        "key2" => "value2"
      )
    );
    return $mycustomconfig;
  ```

  Calling custom configuration:
  ```
  Properties::getOtherProperties("yourconfigname", $key, $subkey = null) : array | string
  ```
  ex: `Properties::getOtherProperties("mycustomconfig", "parent1", "key1")`

  Note
  : If `$subkey` is not supplied then the configuration will return array of `$key`'s value with current environment


  **Environment based config**
  | Config | Description |
  | :----------------- | :-------------------------------------------------------- |
  | `db` | database connection configuration. |
  | `view` | location of your template files. |
  | `assets` | location of your assets files such as images, videos etc. |
  | `showerror` | to show/hide SQL error message in the response body. |

  If your server doesn't enable/support mod_rewrite in .htaccess change `supportHtaccess` value to false and query string parameter `/?endpoint=` will be used as your API endpoint while router path definition in the controller remains the same.
     
  **controller route: `"/"`**
  Example:
  >
  > With htaccess enabled:
  >
  > - simple url: https://yourdomain/yourcontroller
  > - with query string: https://yourdomain/yourcontroller?page=2
  
  > Without htaccess enabled:
  > 
  > - simple url: https://yourdomain/?endpoint=yourcontroller
  > - with query string: https://yourdomain/?endpoint=yourcontroller&page=2
  >

  **controller route: `"/status/:status"`**
  Example:
  >
  > With htaccess enabled:
  >
  > - simple url: https://yourdomain/yourcontroller/status/active
  > - with query string: https://yourdomain/yourcontroller/status/active?page=2

  > Without htaccess enabled:
  >
  > - simple url: https://yourdomain/?endpoint=yourcontroller/status/active
  > - with query string: https://yourdomain/?endpoint=yourcontroller/status/active&page=2

  If controller or method doesn't exist then endpoint returns error code 404.
  The error will be displayed/hidden depends on `showerror` configuration  

**2. Router & Controller**
<hr/>

  Apitizer doesn't use separate router file for the API endpoint. Your controller file name is used as the endpoint and the request methods are defined as a public function name.
  Follow the RESTful API resource naming principles of being clean, clear, and simple. ([https://restfulapi.net/resource-naming](https://restfulapi.net/resource-naming)).

  How to create controller like how OOP concept works in PHP
  
  1. Define `namespace Controller`
  2. Define `use Core\Controller`
  3. Extends your class from Controller
  4. Import other class by: `use namespace\Classname`.
  5. If your other class name is the same with controller use alias: `use namespace\SameClassNameAsController as NewAlias`
  6. Save it under controller directory

  `controller/employees.php`

  ```
  namespace Controller;
  use Core\Controller;
  use Model\Employees as EmployeesModel; //same class name
  use Helper\Utils;
  use Helper\Mailer;
  use Core\Properties;

  class Employees extends Controller{
    private $model = null;

    function __construct(){
      $this->model = new EmployeesModel();
    }

    public function get(){ //the request method
      $this->map(array(
        "/" => function( $callbackvalues ){ //the base route
          $result = $this->model->getAllEmployees();
          return $this->json( $result );
        },

        "/:id" => function( $callbackvalues ){ //route with parameter
          // construct schema manually
          $schema = array(
              ":id" => Utils::validateString( $callbackvalues["keys"]["id"] )
          );
          $result = $this->model->getEmployeeById( $schema );
          return $this->xml( $result );
        },

        "/firstname" => function( $callbackvalues ){ //another route
          $result = $this->model->getEmployeesFirstName();
          return $this->json( $result );
        },

        "/status/:status" => function( $callbackvalues ){ //longer route with parameter
          // construct schema automatically
          $schema = Utils::constructSchema($callbackvalues["keys"]);
          $result = $this->model->getEmployeeByStatus( $schema );
          return $this->html( "mytemplate",$result );
        }
      ));
    }
  }
  ```

  **Anatomy**

  - **Request Method**
  
      | Method handler | Description |
      | :- | :- |
      | `public function get(): void` | Handles GET method. |
      | `public function post(): void` | Handles POST method. |
      | `public function delete(): void` | Handles DELETE method. |
      | `public function put(): void` | Handles PUT method. |
      | `public function patch(): void` | Handles PATCH method. |

      `public function your_request_method()` is the name of your request methods as a function. In the example above the request method is GET so the handler is `public function get()`.
      <br/>

      > Write only the method handlers you need.

  - **Routing**
  
      | Function | Description |
      | :- | :- |
      | `$this->map(array): void` | Defines your routes array.|

      How to define routes:

      - Base route `/` without trailing endpoint will refer to the controller file name. The API url will be https://yourdomain/employees
     
      - With trailing endpoint.
        Example: `/firstname`. The API url will be https://yourdomain/employees/firstname

      - With parameter `/:yourparametername`. The parameter name must start with a `:` character followed by the parameter name. This will be a placeholder for the actual value that will be inserted into the url.
        Example: `/:id`. The API url will be https://yourdomain/employees/123. The `id` is a parameter name with value 123.
        
      - Url with query string doesn't need to be defined as a specific route. It follows any route as long as the API url matches the specified route pattern. Apitizer handles it as a regular query parameter.
        Examples:

        - Url https://yourdomain/employees?page=2 will be executed under `/` route.
        - Url https://yourdomain/employees/123?page=2 will be executed under `/:id` route.
        - Url https://yourdomain/employees/firstname?page=2 will be executed under `/firstname` route.

         <br/>
         Combination of routes can be defined like /firstname/:x/lastname/:y and the url will be https://yourdomain/employees/firstname/john/lastname/doe or the url can be like this as well https://yourdomain/employees/firstname/john/lastname/doe?page=2

         <br/>
         <br/>

        > Apitizer recognizes the difference between `/:id` and `/firstname` routes. From the example above, if `/firstname` is defined as a route then url like https://yourdomain/employees/firstname will be executed under `/firstname` route. If `/firstname` is not defined as a route then url like https://yourdomain/employees/firstname will be executed under `/:id` route and the value of `id` variable name is firstname

  - <a name="callbackvalues"></a>**Callback values**

      `$callbackvalues` (you can replace it with any variable name you want) is the return value of each route callback.

      | Value | Description |
      | :- | :- |
      | `queries` | Query string that available in the url. |
      | `url` | Details of url. |
      | `headers` | Get http headers. The default is to get all headers containing the "HTTP" key. You can customize what headers to get by modifying it in static/allowed.php in the header section. |
      | `keys` | placeholder/parameter name included in the route. |
      | `data` | data passed via non-GET methods. |
      | `records` | hold result of data fetched from model |

      For example: a route pattern /firstname/:x/lastname/:y with POST request, its url is https://yourdomain/employees/firstname/john/lastname/doe?status=active and the form data is:

      ```
      {
          "email": "johndoe@domain.com",
          "birthdate": "1990-07-09"
      }
      ```

      the `$callbackvalues` will be:

      ```
      Array
        (
            [queries] => Array
                (
                    [status] => active
                )

            [url] => Array
                (
                    [controller] => employees
                    [endpoint] => /employees/firstname/john/lastname/doe
                    [base] => /your-working-directory
                    [method] => GET
                    [url] => http://yourdomain/your-working-directory/employees/firstname/john/lastname/doe
                    [path] => /your-working-directory/employees/firstname/john/lastname/doe
                )

            [headers] => Array
                (
                    [HTTP_USER_AGENT] => PostmanRuntime/7.39.1
                    [HTTP_ACCEPT] => */*
                    [HTTP_CACHE_CONTROL] => no-cache
                    [HTTP_HOST] => yourdomain
                    [HTTP_ACCEPT_ENCODING] => gzip, deflate, br
                    [HTTP_CONNECTION] => keep-alive
                )

            [keys] => Array
                (
                    [x] => john
                    [y] => doe
                )

            [data] => Array
                (
                    [email] => johndoe@domain.com
                    [birthdate] => 1990-07-09
                )
            [records] => Array()
        )
      ```

  - **Return value formats**

      Apitizer returns in these formats:

      - | Function                               | Description                    |
        | :------------------------------------- | :----------------------------- |
        | `$this->json( array $result ): string` | returns JSON formatted string. |

        Url: https://yourdomain/employees/10

        Result:

        ```
          {
            "response": 200,
            "message": "OK",
            "result": [
                {
                    "id": 10,
                    "FirstName": "Misha",
                    "LastName": "Geroldi",
                    "Email": "mgeroldi9@google.co.uk",
                    "Status": "INACTIVE"
                }
            ]
          }
        ```

        If `jsonpcallback` is defined in the url then the result will be returned as JSONP.

        Url: https://yourdomain/employees/10?jsonpcallback=mycallback

        Result:

        ```
        mycallback({"response":200,"message":"OK","result":[{"id":10,"FirstName":"Misha","LastName":"Geroldi","Email":"mgeroldi9@google.co.uk","Status":"INACTIVE"}]})
        ```

      - | Function                              | Description                   |
        | :------------------------------------ | :---------------------------- |
        | `$this->xml( array $result ): string` | returns XML formatted string. |

        Url: https://yourdomain/employees/10

        Result:

        ```
          <?xml version="1.0" encoding="UTF-8"?>
          <root>
              <response>200</response>
              <message>OK</message>
              <result>
                  <record>
                      <id>10</id>
                      <FirstName>Misha</FirstName>
                      <LastName>Geroldi</LastName>
                      <Email>mgeroldi9@google.co.uk</Email>
                      <Status>INACTIVE</Status>
                  </record>
              </result>
          </root>
        ```

      - | Function | Description |
        | :- | :- |
        | `$this->html( string $templateName, array $result ): string` | returns HTML formatted string. |

        HTML page template must be provided under `/view` directory. `$templateName` parameter value is a PHP file name without mentioning its path & extension. There is no special template syntax on how to handle the `$result` parameter. It is the same as native PHP.

        Url: https://yourdomain/employees/10

        Result:

        `view/mytemplate.php`

        ```
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Employee list</title>
        </head>
        <body>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?=$_POST["FirstName"]?> <?=$_POST["LastName"]?></td>
                        <td><?=$_POST["Email"]?></td>
                        <td><?=ucfirst(strtolower($_POST["Status"]))?></td>
                    </tr>
                </tbody>
            </table>
        </body>

        </html>
        ```
**3. Middleware**
<hr/>

  Apitizer has middleware feature that consists of global and controller specific. 

  A. Global middleware
  
  This middleware runs on all endpoints and can modify request before entering controller and/or modify response after entering controller. Global middleware is suitable for Logger, Authentication and any process required for every request & response from the endpoint. This middleware works like layers of an onion that can be illustrated as follows:

  | endpoint url -> | middleware 1 -> | middleware 2 -> | controller -> | middleware 2 -> | middleware 1 -> | output |
  | :- | :- | :- | :- | :- | :- | :- |

  How to create global middleware:
  
  1. Define `namespace Middleware`
  2. Define `use Core\Middleware`
  3. Extends your class from Middleware
  4. Import other class by: `use namespace\Classname`.
  5. If your other class name is the same with middleware use alias: `use namespace\SameClassNameAsController as NewAlias`
  6. Save it under middleware directory

  `middleware/logger.php`

  ```
  namespace Middleware;
  use Core\Middleware;
  class Logger extends Middleware{
     public function get($sequence){
        if ($sequence == "before"){
            //do something before middleware reaches controller
        }else{
           //do something after middleware reaches controller
        }
     }
  }
  ```

  Register your global middleware:

  `static/middleware.php`

  ```
  $globalMiddlewares = array("Logger", "Middleware2", "MiddlewareN");
  return $globalMiddlewares;
  ```

  B. Controller specific middleware

  This middleware runs only when specific controller is called from API endpoint before executing the logic of controller and/or after executing the logic of controller

  How to create specific controller middleware is the same as global middleware but the only differences are:
  - Name and save this specific controller **with same file name as the Controller**
  - No need to register in `static/middleware.php`

  Middleware plays on the same [$callbackvalues](#user-content-callbackvalues) in Controller map() to manipulate data on request or response.

  | Function | Description |
  | :- | :- |
  | `$this::setData("Array.Tree.As.In.Controller.Callback", $value)` | Set new value |
  | `$this::getData("Array.Tree.As.In.Controller.Callback")` | Get value |
  
**4. Model**
<hr/>

  Model uses PDO & perepared statement for database functionality. In model has one function to execute & return SQL query result back to the controller.
  Create model:

  `model/employeesmodel.php`

  ```
  class EmployeesModel extends Model{

    public function getAllEmployees(){
      $query = "select * from employee";
      return $this->execute( $sql );
    }

    public function getEmployeeById( $parameter ){
      $query = "select * from employee where id = :id";
      return $this->execute( $sql, $parameter );
    }
  }
  ```

  **Anatomy**

  | Function | Description |
  | :- | :- |
  | `$this->execute( string $sql, [array $parameter = array()] ): array` | execute sql query string. |

  `$parameter`: an optional array that its structure follows PDO array named values.
  Example:

  ```
  $parameter = array(":id" => "10");
  ```

### Helper

Apitizer includes default helper functions for basic usage under static `Utils` class. This class is just a collection of functions that doesn't implement special design pattern. You can modify to add your own custom function into this class and call it by `Utils::yourFunction()`.

| Function | Description |
| :-| :-|
| `responseHeader(string $code, [boolean $textonly = false]): string` | Returns response header. If `$textonly` is true then it returns response text only of `$code` parameter. |
| `response( string $code, [array $msg = []]): string` | Returns JSON formated with response code, reponse message and array of results. |
| `generateRandomString(int $length = 6): string` | Return random string with default length is 6 characters. |
| `generatePassword(int $length = 8, [string $pwd = null]): string`   | Returns a hashed string with default length is 8 characters. If `$pwd` is null then it will hash random string from `generateRandomString` function. If `$pwd` is not null it returns hashed `$pwd`. |
| `constructSchema(...$param): array` | This function is for auto constructing a PDO array named values needed for model parameter. |
| `validateString(string $str): string` | Validate a string before processed into SQL query. |
| `validateXmlString(string $str): string` | Validate a string for XML. |
| `formatDate(string $date,string $format): string` | A date format based on local time configuration. |
| `getHeaders()` | Get request headers. You can customize what headers to get by modifying it in static/allowed.php in the header section. |
| `getCurrentDir()` | Get current directory. |
| `renderTemplate( string $template , [array $data = null] ): string` | Run a PHP page with post `$data` and render it as HTML. |
| `testQuery(string $sql, [array $params = array()])` | Test your query to see what your query will look like before executing it in the model. |
