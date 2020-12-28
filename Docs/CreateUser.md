**Create User**
----
Registers a new user with [OutlawDesigns](https://outlawdesigns.io) and returns json data about that new user.

* **URL**

  /user/

* **Method:**

  `POST`

*  **URL Params**

  None

* **Data Params**
  **Required:**
  ```
  {
      "username": "bogus_user",
      "password": "{MD5_HASH_OF_DESIRED_PASSWORD}",
  }
  ```
  **Optional**
  ```
  {
      "first_name": null,
      "last_name": null,
      "dob": null,
      "street_address": null,
      "city": null,
      "state": null,
      "email": null,
      "phone": null,
      "domain": null,
  }
  ```

* **Success Response:**

  * **Code:** 200 <br />
    **Content:**
    ```
    {
        "UID": "6597542",
        "username": "bogus_user",
        "password": "null",
        "auth_token": "null",
        "token_expiration": "2020-12-28 04:00:03",
        "secret": null,
        "ip_address": "172.17.0.1",
        "mac_address": null,
        "lat": null,
        "lon": null,
        "created_date": "2019-12-10 11:50:13",
        "created_by": null,
        "updated_date": "2020-12-28 01:14:17",
        "updated_by": null,
        "last_login": null,
        "status_id": null,
        "first_name": null,
        "last_name": null,
        "dob": null,
        "street_address": null,
        "city": null,
        "state": null,
        "email": null,
        "phone": null,
        "domain": null,
        "login_attempts": "2",
        "lock_out": null,
        "lock_out_expiration": null
    }
    ```

* **Error Response:**

  * **Code:** 200 <br />
    **Content:** `{ error : "Access Denied. No Token Present." }`

    OR

    * **Code:** 200 <br />
      **Content:** `{ error : "Access Denied. Invalid Token." }`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "/user/",
      dataType: "json",
      type : "POST",
      success : function(r) {
        console.log(r);
      }
    });
  ```
