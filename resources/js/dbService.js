const dbName = 'POSDatabase';
const dbVersion = 1;
let db;

function openDatabase() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(dbName, dbVersion);

    request.onupgradeneeded = event => {
      db = event.target.result;
    };

    request.onsuccess = event => {
      db = event.target.result;
      resolve(db);
    };

    request.onerror = event => {
      reject(event.target.errorCode);
    };
  });
}

function ensureObjectStore(storeName, keyPath = 'id', indexes = []) {
  return new Promise((resolve, reject) => {
    if (db.objectStoreNames.contains(storeName)) {
      resolve();
    } else {
      const version = db.version + 1;
      db.close();
      const request = indexedDB.open(dbName, version);

      request.onupgradeneeded = event => {
        db = event.target.result;
        const store = db.createObjectStore(storeName, { keyPath: keyPath });
        indexes.forEach(index => store.createIndex(index.name, index.keyPath, { unique: index.unique || false }));
      };

      request.onsuccess = event => {
        db = event.target.result;
        resolve();
      };

      request.onerror = event => {
        reject(event.target.errorCode);
      };
    }
  });
}

function getAll(storeName) {
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(storeName, 'readonly');
    const store = transaction.objectStore(storeName);
    const request = store.getAll();

    request.onsuccess = () => {
      resolve(request.result);
    };

    request.onerror = () => {
      reject(request.error);
    };
  });
}

function getItemByAttributes(storeName, attributes) {
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(storeName, 'readonly');
    const store = transaction.objectStore(storeName);
    const indexKeys = Object.keys(attributes);
    const results = [];

    let completed = 0;
    indexKeys.forEach(key => {
      const index = store.index(key);
      const request = index.getAll(attributes[key]);

      request.onsuccess = () => {
        results.push(...request.result);
        completed++;
        if (completed === indexKeys.length) {
          const uniqueResults = Array.from(new Set(results.map(item => item.id)))
                                    .map(id => results.find(item => item.id === id));
          resolve(uniqueResults);
        }
      };

      request.onerror = () => {
        reject(request.error);
      };
    });
  });
}

function addOrUpdateItem(storeName, item) {
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(storeName, 'readwrite');
    const store = transaction.objectStore(storeName);
    const request = store.put(item);

    request.onsuccess = () => {
      resolve();
    };

    request.onerror = () => {
      reject(request.error);
    };
  });
}

function deleteItem(storeName, id) {
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(storeName, 'readwrite');
    const store = transaction.objectStore(storeName);
    const request = store.delete(id);

    request.onsuccess = () => {
      resolve();
    };

    request.onerror = () => {
      reject(request.error);
    };
  });
}

function tableExists(storeName) {
  return db.objectStoreNames.contains(storeName);
}

export {
  openDatabase,
  ensureObjectStore,
  getAll,
  getItemByAttributes,
  addOrUpdateItem,
  deleteItem,
  tableExists
};
