interface TimeSession {
  start: string;
  end?: string;
  duration: number;
}

export const useActivityTracking = () => {
  const accumulatedTime = ref(0);
  const isActive = ref(false);
  const lastActivityTime = ref(Date.now());
  const lastSyncTime = ref(Date.now());
  const sessions = ref<TimeSession[]>([]);
  const currentSession = ref<TimeSession | null>(null);

  // Activity detection constants
  const INACTIVE_THRESHOLD = 60000; // 1 minute
  const SYNC_INTERVAL = 60000; // 1 minute
  const HEARTBEAT_INTERVAL = 30000; // 30 seconds

  const { $echo, $api } = useNuxtApp();
  const authStore = useAuthStore();

  // Store time data in localStorage to persist across page reloads
  const saveTimeData = () => {
    localStorage.setItem("timeTracking", JSON.stringify({
      accumulatedTime: accumulatedTime.value,
      lastSyncTime: lastSyncTime.value,
      sessions: sessions.value,
      currentSession: currentSession.value
    }));
  };

  // Load saved time data
  const loadTimeData = () => {
    const savedData = localStorage.getItem("timeTracking");
    if (savedData) {
      const data = JSON.parse(savedData);
      accumulatedTime.value = data.accumulatedTime;
      lastSyncTime.value = data.lastSyncTime;
      sessions.value = data.sessions;
      currentSession.value = data.currentSession;
    }
  };

  // Track user activity
  const updateActivity = () => {
    const now = Date.now();
    const timeSinceLastActivity = now - lastActivityTime.value;

    // If user was inactive and is now active
    // if (!isActive.value && timeSinceLastActivity < INACTIVE_THRESHOLD) {
    if (!isActive.value) {
      startNewSession();
    }
    // If user was active and is now inactive
    else if (isActive.value && timeSinceLastActivity >= INACTIVE_THRESHOLD) {
      endCurrentSession();
    }

    if (isActive.value && currentSession.value) {
      updateAccumulatedTime();
    }

    lastActivityTime.value = now;
    isActive.value = timeSinceLastActivity < INACTIVE_THRESHOLD;
  };

  // Start a new session
  const startNewSession = () => {
    currentSession.value = {
      start: new Date().toISOString(),
      duration: 0
    };
  };

  // End current session
  const endCurrentSession = () => {
    if (currentSession.value) {
      currentSession.value.end = new Date().toISOString();
      sessions.value.push({ ...currentSession.value });
      currentSession.value = null;
      saveTimeData();
    }
  };

  // Update accumulated time
  const updateAccumulatedTime = () => {
    if (currentSession.value) {
      const now = Date.now();
      const increment = now - lastActivityTime.value;
      accumulatedTime.value += increment;
      currentSession.value.duration += increment;
      saveTimeData();
    }
  };

  // Sync with backend
  const syncWithBackend = async () => {
    try {
      const response = await $api.post("/user/time/sync", {
        accumulated_time: Math.floor(accumulatedTime.value / 1000), // Convert to seconds
        last_sync_time: new Date(lastSyncTime.value).toISOString(),
        sessions: sessions.value
      });

      if (response) {
        lastSyncTime.value = Date.now();
        sessions.value = []; // Clear synced sessions
        saveTimeData();
      }
    } catch (error) {
      console.error("Failed to sync time data:", error);
    }
  };

  // Send heartbeat through WebSocket
  const sendHeartbeat = () => {
    if ($echo && isActive.value) {
      // $echo.private(`private-notification.${ authStore.user?.id }`)
      //   .whisper("heartbeat", {
      //     timestamp: new Date().toISOString(),
      //     accumulated_time: accumulatedTime.value
      //   });
      // $echo.connector.pusher.send_event("client-heartbeat", {
      //   timestamp: new Date().toISOString(),
      //   accumulated_time: accumulatedTime.value
      // });
    }
  };

  // Setup activity listeners
  const setupActivityListeners = () => {
    const events = [
      "mousemove",
      "mousedown",
      "keypress",
      "DOMMouseScroll",
      "mousewheel",
      "touchmove",
      "MSPointerMove"
    ];

    events.forEach(event => {
      window.addEventListener(event, updateActivity, { passive: true });
    });

    // Set up intervals
    const heartbeatInterval = setInterval(sendHeartbeat, HEARTBEAT_INTERVAL);
    const syncInterval = setInterval(syncWithBackend, SYNC_INTERVAL);

    // Initial load
    loadTimeData();
    updateActivity();

    // Cleanup
    onUnmounted(async () => {
      events.forEach(event => {
        window.removeEventListener(event, updateActivity);
      });
      clearInterval(heartbeatInterval);
      clearInterval(syncInterval);
      endCurrentSession();
      await syncWithBackend();
    });
  };

  // Format time for display
  const formatTime = (milliseconds: number): string => {
    const minutes = Math.floor(milliseconds / 60000);
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;

    if (hours > 0) {
      return `${ hours }h ${ remainingMinutes }m`;
    }
    return `${ remainingMinutes }m`;
  };

  // Computed properties
  const formattedTotalTime = computed(() => formatTime(accumulatedTime.value));
  const activeStatus = computed(() => isActive.value ? "Active" : "Inactive");

  onMounted(() => {
    setupActivityListeners();
  });

  return {
    accumulatedTime,
    isActive,
    formattedTotalTime,
    activeStatus,
    currentSession,
    formatTime
  };
};
