
<template>
<div>
    <input
      type="text"
      id="repositorySearch"
      v-model="searchQuery"
      @input="searchRepositories"
      class="mt-1 p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300 w-full dark:text-gray-600 text-white"
      placeholder="Start typing name of missing repository..."
    />

    <div v-if="searchResults.length" class="absolute z-10 mt-2 w-full bg-white border rounded-md shadow-md">
      <ul>
        <li
          v-for="repo in searchResults"
          :key="repo.id"
          @click="selectRepository(repo)"
          class="px-4 py-2 cursor-pointer hover:bg-blue-100 dark:text-gray-600 text-white max-w-full"
        >
          {{ repo.full_name }}
        </li>
      </ul>
    </div>

    <div v-if="selectedRepository" class="mt-2">
      <p class="text-gray-700 dark:text-white">Selected Repository: {{ selectedRepository.full_name }}</p>
      <button class="bg-green-400 text-white p-3">Add repository to Open Pledge</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: "",
      searchResults: [],
      selectedRepository: null,
      loading: false
    };
  },
  methods: {
    searchRepositories: debounce(function () {
      if (this.searchQuery.length >= 3) {
        this.loading = true
        axios.get(`/github/repositories?q=${this.searchQuery}`)
        .then((response) => {
            this.searchResults = response.data.items;
        }).finally(() => {
            this.loading = false
        });
      } else {
        this.searchResults = [];
      }
    },400),
    selectRepository(repo) {
      this.selectedRepository = repo;
      this.searchResults = [];
    },
  },
};

function debounce(func, wait) {
  let timeout;
  return function() {
    const context = this;
    const args = arguments;
    const later = function() {
      timeout = null;
      func.apply(context, args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}
</script>