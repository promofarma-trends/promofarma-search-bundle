PromoFarmaTrends_Search
=================

A Symfony project created on June 4, 2017, 6:28 am.

This project is created to use the SearchBundle as an API.
All the gotten results in this bundle are supposed to be shown in another FrontEnd Bundle.

This Bundle works with ElasticSearch and with the FOSElastica repository. 
This Repository allows us to work with an ElasticSearch PHP integration in Symfony2 using Elastica.


`ssh -l root -i ~/.ssh/prod_provision_key.pem ec2-176-34-149-205.eu-west-1.compute.amazonaws.com`

`ansible-playbook --private-key $HOME/.ssh/prod_provision_key.pem -u root -i ec2-176-34-149-205.eu-west-1.compute.amazonaws.com, deploy.yml`


**IP: 176.34.149.205/app.php/mostSpokenTopicsOfMonth**

**IP: 176.34.149.205/app.php/mostRatedTopicsOfTheMonth**

**IP: 176.34.149.205/app.php/evolutionMostSpokenTopic**

**IP: 176.34.149.205/app.php/lastPost**

**IP: 176.34.149.205/app.php/searchInPosts**