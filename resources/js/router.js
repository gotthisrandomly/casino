import { createRouter, createWebHistory } from 'vue-router';
import GameLobby from './components/GameLobby.vue';
import GamePlayer from './components/GamePlayer.vue';

const routes = [
    {
        path: '/',
        name: 'lobby',
        component: GameLobby
    },
    {
        path: '/game/:id',
        name: 'game',
        component: GamePlayer,
        props: true
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;