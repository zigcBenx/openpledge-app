export const labels = [
    {"label":"Test", "value": "test"},
    {"label":"Feature", "value": "feature"},
    {"label":"Bug", "value": "bug"},
    {"label":"Enhancement", "value": "enhancement"},
    {"label":"Documentation", "value": "documentation"},
    {"label":"Question", "value": "question"},
    {"label":"Invalid", "value": "invalid"},
    {"label":"Duplicate", "value": "duplicate"},
    {"label":"Security", "value": "security"}
];

export const languages = [
    {"label":"Python", "value": "python"},
    {"label":"TypeScript", "value": "typeScript"},
    {"label":"PHP", "value": "php"},
    {"label":"Ruby", "value": "ruby"},
    {"label":"Swift", "value": "swift"},
    {"label":"Java", "value": "java"},
    {"label":"Scala", "value": "scala"}
]

const states = ['open', 'closed'];
const favorite = [true, false];
export const issues = [...new Array(100)].map((item, index) => ({
    id: index,
    state: states[Math.floor(Math.random()*states.length)],
    title: `This Is The Issue Title ${index + 1}`,
    description: "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
    user: {
        username: 'test',
        user_avatar: '/images/avatar.png'
    },
    changed_at: 'Fri Apr 19 2024',
    created_at: 'Wed Apr 17 2024',
    labels: ['bug', 'feature'],
    repository: 'strapi/strapi',
    languages: ['Javascript', 'Java', 'Python', 'Ruby', 'Go'],
    donations: `$${300 + index * 10}`,
    favorite: favorite[Math.floor(Math.random()*favorite.length)],
}))

export const trendingToday = [{
      id: 1,
      title: 'Issue title 1',
      repository: 'strapi/strapi',
      donations: '$3000'
  }, {
      id: 2,
      title: 'Issue title 2',
      repository: 'strapi/strapi',
      donations: '$4000'
  }, {
      id: 3,
      title: 'Issue title 3',
      repository: 'strapi/strapi',
      donations: '$5000'
  }];

export const topContributors = [{
      id: 1,
      user: {
          username: 'test',
          user_avatar: '/images/avatar.png'

      },
      languages: ['Javascript', 'Java', 'Python'],
      issues: 350
  }, {
      id: 2,
      user: {
          username: 'test',
          user_avatar: '/images/avatar.png'
      },
      languages: ['Ruby', 'Java', 'Go'],
      issues: 200
  }];

export const topDonators = [{
      id: 1,
      user: {
          username: 'test',
          user_avatar: '/images/avatar.png'

      },
      languages: ['Javascript', 'Java', 'Python'],
      donations: '$400'
  }, {
      id: 2,
      user: {
          username: 'test',
          user_avatar: '/images/avatar.png'

      },
      languages: ['Ruby', 'Java', 'Go'],
      donations: '$350'
  }];