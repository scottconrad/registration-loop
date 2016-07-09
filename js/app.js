(function(){
if (typeof Object.assign != 'function') {
  Object.assign = function(target) {
    if (target == null) {
      throw new TypeError('Cannot convert undefined or null to object');
    }

    target = Object(target);
    for (var index = 1; index < arguments.length; index++) {
      var source = arguments[index];
      if (source != null) {
        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }
    }
    return target;
  };
}
var store = {'showLoader':true};
if(window && window.RL && window.RL.hasOwnProperty('__state')) store = Object.assign({},window.RL.__state,store);
var doApiRequest = function(param){
  store.showLoader = true;
  var params = {'JSON':1,showLoader:true};
  params = Object.assign({},{
    'JSON':1,'page':store.page
  },params);
  $.getJSON('/',params,function(data){
    store.reviews = data.reviews;
  
    store.showLoader = false;
  });
  
}

store.showLoader = false;
var app = new Vue({
    el:'#app',
    data:store,
    methods:{
        getReviews:function(){
            return store.reviews;
        },
        getTotalRating:function(){
          return store.business_info.total_rating;
        },
        getRating:function(){
          return this.getTotalRating().total_avg_rating;
        },
        getTotalReviews:function(){
            var total = this.getTotalRating().total_no_of_reviews;
            return total;
        },
        getBusinessInfo:function(){
          return this.business_info;
        },
        getBusinessName:function(){
        return this.getBusinessInfo().business_name;
          
        },
        getReviewDate:function(review){
          var m = new moment(review.get_date_of_submission);
          var formattedDate = m.format('M/D/YYYY');
          return formattedDate; 
        },
        updateFromSearchResults:function(data){
          data = this.data;
        },
        getReviewSources:function(){
        return ['Yelp','Google','Molomedia'];
        },
        getReviewSourceById:function(id){
          return this.getReviewSources()[id];
        },
        getReviewCount:function(){
         return this.getTotalReviews();
        },
        getStartingItem:function(){
          return (store.page + 1) * store.perPageAmount;
        },
        getPageAmount:function(){
        return store.perPageAmount;
        },
        getOffset:function(){
          if(store.page == 0) return 0 + 1;
            return (store.perPageAmount * store.page) + 1;
        },
        doesNextPageExist:function(){
          return store.page < (this.getTotalRating().total_no_of_reviews / store.perPageAmount);
        },
        doesPreviousPageExist:function(){
          
          var result = store.page > 0;
          return result;
        },
        populateNextPage:function(e){
           e.preventDefault();
          if(!this.doesNextPageExist()) alert("there are no more results to show");
          store.page++;
          this.doSearch(store);
          return false;
        },
        populatePreviousPage(e){
           e.preventDefault();
          if(store.page <= 0 ) alert("there are no previous pages");
          store.page--;
          this.doSearch(store);
        },
        doSearch:function(){
          doApiRequest(store);
        }
    }
});

})();