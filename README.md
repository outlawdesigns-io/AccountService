
# User Account REST API

## Preamble
The AccountService provides an interface for users to register user accounts [OutlawDesigns.io](https://outlawdesigns.io)...
This service can be used to build reports or client applications in any language that supports making http calls.

## Meta

### Security

This API is accessible only by registered users of [outlawdesigns.io](https://outlawdesigns.io) who present a valid authorization token.
Authorization tokens should be presented as a value of the `auth_token` header.

#### Sample Call
```
curl --location --request GET 'https://api.outlawdesigns.io:9661/authenticate' \
--header 'request_token: YOUR_USERNAME' \
--header 'password: YOUR_PASSWORD'\
```

### Reporting performance or availability problems

Report performance/availability at our [support site](mailto:j.watson@outlawdesigns.io).

### Reporting bugs, requesting features

Please report bugs with the API or the documentation on our [issue tracker](https://github.com/outlawdesigns-io/AccountService/issues).

## Endpoints

### authenticate/

* [Authenticate](./Docs/Authenticate.md)

### verify/

* [VerifyToken](./Docs/VerifyToken.md)

### user/

* [GetAllUsers](./Docs/UpdateUser.md)
* [GetUser](./Docs/GetUser.md)
* [CreateUser](./Docs/CreateUser.md)
* [UpdateUser](./Docs/UpdateUser.md)


### location/

* [GetAllUserLocations](./Docs/GetAllUserLocations.md)
* [GetUserLocation](./Docs/GetUserLocation.md)
