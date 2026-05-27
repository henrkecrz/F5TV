import React, { createContext, useContext, useState } from 'react';
import { 
  User, Profile, Plan, Content, Category, Series, Season, Episode, 
  Subscription, Payment, Upload, WatchHistory, Favorite, Notification, Review 
} from '../types';
import { db } from '../data/mockDatabase';

interface DataContextType {
  users: User[];
  plans: Plan[];
  contents: Content[];
  series: Series[];
  seasons: Season[];
  episodes: Episode[];
  payments: Payment[];
  subscriptions: Subscription[];
  uploads: Upload[];
  categories: Category[];
  watchHistory: WatchHistory[];
  favorites: Favorite[];
  notifications: Notification[];
  reviews: Review[];
  updateUsers: (newUsers: User[]) => void;
  updatePlans: (newPlans: Plan[]) => void;
  updateContents: (newContents: Content[]) => void;
  updateSeries: (newSeries: Series[]) => void;
  updateSeasons: (newSeasons: Season[]) => void;
  updateEpisodes: (newEpisodes: Episode[]) => void;
  updatePayments: (newPayments: Payment[]) => void;
  updateSubscriptions: (newSubscriptions: Subscription[]) => void;
  updateUploads: (newUploads: Upload[]) => void;
  updateCategories: (newCategories: Category[]) => void;
  updateWatchHistory: (newHistory: WatchHistory[]) => void;
  updateFavorites: (newFavs: Favorite[]) => void;
  updateNotifications: (newNotifs: Notification[]) => void;
  updateReviews: (newReviews: Review[]) => void;
  refreshAllData: () => void;
}

const DataContext = createContext<DataContextType | undefined>(undefined);

export const DataProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [users, setUsers] = useState<User[]>(() => db.getUsers());
  const [plans, setPlans] = useState<Plan[]>(() => db.getPlans());
  const [contents, setContents] = useState<Content[]>(() => db.getContents());
  const [series, setSeries] = useState<Series[]>(() => db.getSeries());
  const [seasons, setSeasons] = useState<Season[]>(() => db.getSeasons());
  const [episodes, setEpisodes] = useState<Episode[]>(() => db.getEpisodes());
  const [payments, setPayments] = useState<Payment[]>(() => db.getPayments());
  const [subscriptions, setSubscriptions] = useState<Subscription[]>(() => db.getSubscriptions());
  const [uploads, setUploads] = useState<Upload[]>(() => db.getUploads());
  const [categories, setCategories] = useState<Category[]>(() => db.getCategories());
  const [watchHistory, setWatchHistory] = useState<WatchHistory[]>(() => db.getWatchHistory());
  const [favorites, setFavorites] = useState<Favorite[]>(() => db.getFavorites());
  const [notifications, setNotifications] = useState<Notification[]>(() => db.getNotifications());
  const [reviews, setReviews] = useState<Review[]>(() => db.getReviews());

  const updateUsers = (newUsers: User[]) => {
    db.setUsers(newUsers);
    setUsers(newUsers);
  };

  const updatePlans = (newPlans: Plan[]) => {
    db.setPlans(newPlans);
    setPlans(newPlans);
  };

  const updateContents = (newContents: Content[]) => {
    db.setContents(newContents);
    setContents(newContents);
  };

  const updateSeries = (newSeries: Series[]) => {
    db.setSeries(newSeries);
    setSeries(newSeries);
  };

  const updateSeasons = (newSeasons: Season[]) => {
    db.setSeasons(newSeasons);
    setSeasons(newSeasons);
  };

  const updateEpisodes = (newEpisodes: Episode[]) => {
    db.setEpisodes(newEpisodes);
    setEpisodes(newEpisodes);
  };

  const updatePayments = (newPayments: Payment[]) => {
    db.setPayments(newPayments);
    setPayments(newPayments);
  };

  const updateSubscriptions = (newSubscriptions: Subscription[]) => {
    db.setSubscriptions(newSubscriptions);
    setSubscriptions(newSubscriptions);
  };

  const updateUploads = (newUploads: Upload[]) => {
    db.setUploads(newUploads);
    setUploads(newUploads);
  };

  const updateCategories = (newCategories: Category[]) => {
    db.setCategories(newCategories);
    setCategories(newCategories);
  };

  const updateWatchHistory = (newHistory: WatchHistory[]) => {
    db.setWatchHistory(newHistory);
    setWatchHistory(newHistory);
  };

  const updateFavorites = (newFavs: Favorite[]) => {
    db.setFavorites(newFavs);
    setFavorites(newFavs);
  };

  const updateNotifications = (newNotifs: Notification[]) => {
    db.setNotifications(newNotifs);
    setNotifications(newNotifs);
  };

  const updateReviews = (newReviews: Review[]) => {
    db.setReviews(newReviews);
    setReviews(newReviews);
  };

  const refreshAllData = () => {
    setUsers(db.getUsers());
    setPlans(db.getPlans());
    setContents(db.getContents());
    setSeries(db.getSeries());
    setSeasons(db.getSeasons());
    setEpisodes(db.getEpisodes());
    setPayments(db.getPayments());
    setSubscriptions(db.getSubscriptions());
    setUploads(db.getUploads());
    setCategories(db.getCategories());
    setWatchHistory(db.getWatchHistory());
    setFavorites(db.getFavorites());
    setNotifications(db.getNotifications());
    setReviews(db.getReviews());
  };

  return (
    <DataContext.Provider value={{
      users,
      plans,
      contents,
      series,
      seasons,
      episodes,
      payments,
      subscriptions,
      uploads,
      categories,
      watchHistory,
      favorites,
      notifications,
      reviews,
      updateUsers,
      updatePlans,
      updateContents,
      updateSeries,
      updateSeasons,
      updateEpisodes,
      updatePayments,
      updateSubscriptions,
      updateUploads,
      updateCategories,
      updateWatchHistory,
      updateFavorites,
      updateNotifications,
      updateReviews,
      refreshAllData
    }}>
      {children}
    </DataContext.Provider>
  );
};

export const useData = () => {
  const context = useContext(DataContext);
  if (context === undefined) {
    throw new Error('useData must be used within a DataProvider');
  }
  return context;
};

// Expose discrete hooks requested by task 12
export const useUsers = () => useData().users;
export const usePlans = () => useData().plans;
export const useContents = () => useData().contents;
export const useSeries = () => useData().series;
export const useSeasons = () => useData().seasons;
export const useEpisodes = () => useData().episodes;
export const usePayments = () => useData().payments;
export const useSubscriptions = () => useData().subscriptions;
export const useUploads = () => useData().uploads;
