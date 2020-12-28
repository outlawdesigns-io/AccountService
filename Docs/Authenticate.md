**Authenticate**
----
Authenticate an [OutlawDesigns](https://outlawdesigns.io) user, granting an `auth_token` that can be presented to services that require privileged access. Returns a json object containing the issued `auth_token` and a secret that can be used decode it.

* **URL**

  /authenticate/

* **Method:**

  `GET`

* **Header Params**
  **Required:**
   `request_token=[string] -- The username to authenticate`
   `password=[string]`

*  **URL Params**

  None

* **Data Params**

  None

* **Success Response:**

  * **Code:** 200 <br />
    **Content:**
    ```
    {
        "token": "eyJ0eXAiOiJKV1QiCJhbGciOiJIUzI1NiJ9.eyJpcCI6IjE3Mi4xNy4wLjEiLCJ1c2VybmFtZSI6InNlcnZpY2Vfd29ya2VyIiwibGF0IjpudWxsLCJsb25nIjpudWxsLCJzYWx0Ijo3NzN9.FYjKLnbPKfiNG-fBujYEVIzSJsM6-OlLwwHfmpqAT0",
        "secret": "32a4bb7b1898555fc0d27bd01198d8"
    }
    ```

* **Error Response:**

  * **Code:** 200 <br />
    **Content:** `{ "error": "Missing credentials" }`

    OR

    * **Code:** 200 <br />
      **Content:** `{ error : "Access Denied. Invalid Token." }`

    OR

    * **Code:** 200 <br />
      **Content:** `{ "error": "Invalid Username" }`

    OR

    * **Code:** 200 <br />
      **Content:** `{ "error": "Invalid Credentials. Event Logged" }`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "/authenticate/",
      dataType: "json",
      type : "GET",
      success : function(r) {
        console.log(r);
      }
    });
  ```
