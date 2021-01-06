PHP Godaddy Appraisal Check


GoDaddy launched its first Domain Appraisals since 2017. If you dont know what is domain appraisal is, me too. LoL. So basicly it is like a tool to predicting the value of the domain name on the market. Its usefull when you want to buy an existing domain name and try to figure out its estimated price. 

GoDaddy provide API service for developer to access their APIs to check the appraisal. To access the API you need a developer account. 
But, there is limitation when accessing their API service. The  API uses a similar criterion, called a "rate limit,". A user only capable to query for 20 domains at a max. You can retry the request after 49seconds.


This limitation can be bypassed by using multiple API key.

Todo: Create developer account. Go to https://developer.godaddy.com/. Generate a pair of API key ( access key and secret key) in production environment.  Repeat the process to gain more API key. Add the API key to variable $keys.

From my experience, 5 key pairs is safe enough to check bulk file containing more than 100 domains :)


*Tested with PHP5. Yes PHP5
