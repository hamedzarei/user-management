local Json = require("JSON")
local jwt = require("jwt")
local validators = require("jwt-validators")

function string.starts(String,Start)
    return string.sub(String,1,string.len(Start))==Start
end
function string.ends(String,End)
    return End=='' or string.sub(String,-string.len(End))==End
end


-- OPTIONS
local allow_headers = "token, Content-Type, Accept, X-Requested-With, Origin, x-role, x-api-key, Access-Control-Allow-Headers, Access-Control-Allow-Origin, Cache-Control"
local allow_methods = "GET, PUT, POST, DELETE, HEAD, PATCH"
ngx.header.Content_Type = "application/json"
ngx.header['Content-Type'] = 'application/json'
ngx.header["Access-Control-Allow-Origin"] = "*"

if ngx.req.get_method() == "OPTIONS" then

    ngx.header["Access-Control-Allow-Headers"] = allow_headers
    ngx.header["Access-Control-Allow-Methods"] = allow_methods
    --ngx.header["Access-Control-Allow-Credentials"] = "true"
    ngx.header["Access-Control-Allow-Headers"] =  allow_headers
    ngx.status = ngx.HTTP_OK
    ngx.say(Json:encode({message = "OK"}))
    ngx.exit(ngx.HTTP_OK)
end

if (ngx.var.service == 'auth' and string.starts(ngx.var.uri,'/tokens') and ngx.req.get_method() == "POST") or
   (ngx.var.service == 'auth' and string.starts(ngx.var.uri, '/users') and ngx.req.get_method() == "POST") or
    (ngx.var.service == 'auth' and string.starts(ngx.var.uri, '/otp') and ngx.req.get_method() == "POST") then
  return
end


function readAll(file)
    local f = io.open(file, "rb")
    local content = f:read("*all")
    f:close()
    return content
end

function jwt_verified(key, jwt_obj)
    local verified = jwt:verify_jwt_obj(key, jwt_obj, {
        __jwt = validators.require_one_of({ "iat", "exp" }),
        iat = validators.opt_is_not_before(),
        exp = validators.opt_is_not_expired()
    })
    return verified
end

function isJwtValid(valid, verified)
    --    if (valid and verified) then
    if (valid) then
        return true
    else
        return false
    end
end


function fill_x_role(value)
    ngx.req.set_header("x-role", value)
--    ngx.var.auth_x_scopes = value
end

function fill_x_user_id(value)
    ngx.req.set_header("x-user-id", value)
--    ngx.var.auth_x_scopes = value
end


local ok_read, key = pcall(readAll, '/etc/nginx/key/key.pub')
if not ok_read then
    ngx.say(Json:encode({error = "public key file not exist"}))
   -- ngx.status = 500
   -- ngx.exit(500)
    return
end


local headers = ngx.req.get_headers()

local token = headers['token']

local jwt_obj = jwt:load_jwt(token)

local ok_verified, verified = pcall(jwt_verified, key, jwt_obj)

if not ok_verified then
    ngx.say(Json:encode({error = "error on verification"}))
   -- ngx.status = 500
   -- ngx.exit(500)
    return
end

if (not isJwtValid(verified['valid'], verified['verified'])) then
    ngx.status = ngx.HTTP_UNAUTHORIZED
    ngx.say(Json:encode({error = "token not verified"}))
    ngx.exit(ngx.HTTP_UNAUTHORIZED)
end


--fill_x_role(verified['payload']['role'])
fill_x_user_id(verified['payload']['uid'])
--
--
--ngx.say(Json:encode(verified['payload']))
--
--local h = "1"
--ngx.say("hello")
--ngx.say(Json:encode({key = "value"}))

if ngx.var.service == 'token' then
    ngx.say(Json:encode({user_id = verified['payload']['uid']}))
end

