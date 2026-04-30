export const getStoredToken = () => localStorage.getItem("token");

export const getStoredUser = () => {
  try {
    const rawUser = localStorage.getItem("user");
    return rawUser ? JSON.parse(rawUser) : null;
  } catch {
    return null;
  }
};

export const getUserRole = (user = getStoredUser()) => {
  return user?.user?.role ?? user?.role ?? null;
};

export const setStoredAuth = ({ token, user }) => {
  if (token) {
    localStorage.setItem("token", token);
  }

  if (user) {
    localStorage.setItem("user", JSON.stringify(user));
  }
};

export const clearStoredAuth = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
};

const looksLikeUser = (value) => {
  return Boolean(
    value &&
      typeof value === "object" &&
      !Array.isArray(value) &&
      ("id" in value || "name" in value || "email" in value || "role" in value)
  );
};

export const extractAuthPayload = (payload) => {
  const data = payload?.data && typeof payload.data === "object" ? payload.data : null;

  return {
    token: data?.token ?? payload?.token ?? null,
    user: data?.user ?? payload?.user ?? (looksLikeUser(data) ? data : null),
  };
};
