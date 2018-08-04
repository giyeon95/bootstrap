var peoplenames = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
    queryTokenizer : Bloodhound.tokenizers.whitespace,
    prefetch: {
        url: './json/peoplenames.json',
        filter:function(list) {
            return $.map(list, function(peoplename) {
                return{ name:peoplename};
            });
        }
    }
});
peoplenames.initialize();

var peoples = new Bloodhound( {
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
    queryTokenizer : Bloodhound.tokenizers.whitespace,
    prefetch: './json/peoples3.json',
});
peoplenames.initialize();

/*only objects in the list add  파 빨 초 회 주*/
var elt = $('.inputboxContainer > input');
elt.tagsinput({
    tagClass: function(item) {
        switch (item.continent) {
            case 'girl' : return 'label label-primary';
            case 'boy' : return 'label label-danger label-important';
            
        }
    },
    itemValue:'value',
    itemText:'text',
    
    typeaheadjs: [
        {
            hint:false,
            highlight:true,
            minLength:1
        },
        {
            name:'peoples3',
            displayKey:'text',
            source:peoples.ttAdapter()
        }
    ]
});

elt.tagsinput('add', { "value": 1 , "text": "Rachel"   , "continent": "girl" });
elt.tagsinput('add', { "value": 2 , "text": "Sunny"  , "continent": "boy"   });
elt.tagsinput('add', { "value": 3 , "text": "Melissa"      , "continent": "girl" });
elt.tagsinput('add', { "value": 4, "text": "Victoria"     , "continent": "boy"      });
elt.tagsinput('add', { "value": 5, "text": "Katrina"       , "continent": "girl"    });

